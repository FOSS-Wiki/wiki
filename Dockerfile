# Copyright 2026 FOSS Wiki and Contributors

# Primary maintainer: atmois <info@atmois.com>

# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#     http://www.apache.org/licenses/LICENSE-2.0

# Builder Stage
FROM php:8.5-fpm-alpine3.24 AS builder
SHELL ["/bin/ash", "-eo", "pipefail", "-c"]

# Install PHP Extensions
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/

RUN set -eux && \
    install-php-extensions \
        apcu \
        calendar \
        exif \
        gd \
        intl \
        luasandbox \
        mysqli \
        pdo_mysql \
        redis \
        wikidiff2 \
        zip && \
    apk add --no-cache --virtual .strip-deps binutils && \
    find /usr/local/lib/php/extensions -name '*.so' -exec strip --strip-unneeded {} + && \
    apk del .strip-deps

# Mediawiki Setup Stage
FROM php:8.5-fpm-alpine3.24 AS mediawiki
SHELL ["/bin/ash", "-eo", "pipefail", "-c"]

ENV COMPOSER_NO_AUDIT=1 \
    COMPOSER_NO_SECURITY_BLOCKING=1 \
    COMPOSER_IGNORE_PLATFORM_REQS=1 \
    COMPOSER_HOME=/root/.composer \
    COMPOSER_CACHE_DIR=/root/.composer/cache

RUN set -eux && \
    apk add --no-cache \
        ca-certificates \
        git \
        gnupg \
        python3 \
        unzip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN mkdir -p /var/www/wiki/mediawiki
WORKDIR /var/www/wiki

COPY wiki/composer.json /var/www/wiki/composer.json
RUN --mount=type=cache,target=/root/.composer/cache \
    composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist

WORKDIR /var/www/wiki/mediawiki

# Download and verify MediaWiki
ARG MEDIAWIKI_MAJOR_VERSION
ARG MEDIAWIKI_VERSION
RUN --mount=type=cache,target=/tmp/mediawiki-cache \
    set -eux && \
    TARBALL="/tmp/mediawiki-cache/mediawiki-${MEDIAWIKI_VERSION}.tar.gz" && \
    SIGFILE="/tmp/mediawiki-cache/mediawiki-${MEDIAWIKI_VERSION}.tar.gz.sig" && \
    if [ ! -f "$TARBALL" ]; then \
        curl -fSL --retry 3 --retry-all-errors --retry-delay 3 -C - \
            "https://releases.wikimedia.org/mediawiki/${MEDIAWIKI_MAJOR_VERSION}/mediawiki-${MEDIAWIKI_VERSION}.tar.gz" -o "$TARBALL.tmp" && \
        mv "$TARBALL.tmp" "$TARBALL"; \
    fi && \
    if [ ! -f "$SIGFILE" ]; then \
        curl -fSL --retry 3 --retry-all-errors --retry-delay 3 -C - \
            "https://releases.wikimedia.org/mediawiki/${MEDIAWIKI_MAJOR_VERSION}/mediawiki-${MEDIAWIKI_VERSION}.tar.gz.sig" -o "$SIGFILE.tmp" && \
        mv "$SIGFILE.tmp" "$SIGFILE"; \
    fi && \
    GNUPGHOME="$(mktemp -d)" && \
    export GNUPGHOME && \
    curl -fsSL "https://www.mediawiki.org/keys/keys.txt" | gpg --import && \
    gpg --batch --verify "$SIGFILE" "$TARBALL" && \
    tar -x --strip-components=1 -f "$TARBALL" && \
    gpgconf --kill all && \
    rm -rf "$GNUPGHOME"

# Install Extensions
ARG MEDIAWIKI_BRANCH
COPY wiki/extensions.json wiki/install_extensions.py /tmp/
RUN --mount=type=cache,target=/root/.composer/cache \
    python3 /tmp/install_extensions.py

# Install Citizen
ARG CITIZEN_VERSION
RUN git clone --branch "v${CITIZEN_VERSION}" --single-branch --depth 1 \
        https://github.com/StarCitizenTools/mediawiki-skins-Citizen.git \
        /var/www/wiki/mediawiki/skins/Citizen

COPY wiki/composer.local.json ./composer.local.json
RUN --mount=type=cache,target=/root/.composer/cache \
    composer update --no-dev --optimize-autoloader --no-scripts --prefer-dist

# Cleanup
RUN rm -rf \
        tests/ \
        docs/ \
        mw-config/ \
        maintenance/dev/ \
        maintenance/benchmarks/ \
        vendor/*/*/tests/ \
        vendor/*/*/test/ \
        vendor/*/*/.git* \
        skins/*/.git* \
        skins/*/tests/ \
        extensions/*/.git* \
        extensions/*/tests/ \
        extensions/*/composer.phar \
        extensions/*/vendor/*/*/tests/ \
        extensions/*/vendor/*/*/test/ \
        extensions/*/vendor/*/*/.git* \
        extensions/Scribunto/includes/Engines/LuaStandalone/binaries \
        composer.local.json \
        composer.lock && \
    find . -name "*.md" -delete && \
    find . -name "*.txt" -not -path "*/i18n/*" -not -path "./vendor/*" -delete

# Final Stage
FROM php:8.5-fpm-alpine3.24 AS final
SHELL ["/bin/ash", "-eo", "pipefail", "-c"]

LABEL maintainer="info@atmois.com" \
      org.opencontainers.image.title="FOSS Wiki" \
      org.opencontainers.image.description="FOSS Wiki Docker Image"

# Install Runtime Dependencies
RUN set -eux && \
    apk add --no-cache \
        freetype \
        icu-libs \
        imagemagick \
        libavif \
        libjpeg-turbo \
        libpng \
        librsvg \
        libthai \
        libwebp \
        libxpm \
        libzip \
        lua5.1-libs \
        lz4-libs \
        python3 \
        rsvg-convert

COPY --link --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --link --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --link --chmod=644 wiki/php.ini /usr/local/etc/php/conf.d/custom.ini

# Fail the build if php can't load extensions
RUN out="$(php -m 2>&1 >/dev/null)"; \
    if [ -n "$out" ]; then echo "$out"; exit 1; fi

# Create mediawiki:mediawiki and setup permissions
RUN addgroup -g 1000 -S mediawiki && \
    adduser -u 1000 -S mediawiki -G mediawiki && \
    mkdir -p /var/www/wiki/cache /var/www/wiki/sitemap && \
    touch /var/www/wiki/sitemap/sitemap-index-foss.wiki.xml && \
    ln -s /var/www/wiki/sitemap/sitemap-index-foss.wiki.xml /var/www/wiki/sitemap.xml && \
    chown -R mediawiki:mediawiki /var/www/wiki && \
    chmod -R 775 /var/www/wiki/sitemap && \
    chmod -R 770 /var/www/wiki/cache

USER mediawiki
WORKDIR /var/www/wiki

COPY --link --chown=1000:1000 --from=mediawiki /var/www/wiki .
COPY --link --chown=1000:1000 --chmod=744 wiki/robots.txt ./robots.txt
COPY --link --chown=1000:1000 --chmod=744 wiki/security-at-fosswiki_public.asc ./security-at-fosswiki_public.asc
COPY --link --chown=1000:1000 --chmod=744 wiki/.well-known ./.well-known
COPY --link --chown=1000:1000 wiki/configs/ ./configs/
COPY --link --chown=1000:1000 wiki/LocalSettings.php ./mediawiki/LocalSettings.php

RUN ln -sf ./.well-known/security.txt ./security.txt

# Expose Port for FastCGI
EXPOSE 9000

HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD ["php-fpm", "-t"]
