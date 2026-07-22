# Update OpenSearch index configuration in MediaWiki
opensearch-update:
    #!/usr/bin/env bash
    set -euo pipefail
    just _verify-file compose.yaml
    just _check-containers

    docker compose exec mediawiki php /var/www/wiki/mediawiki/maintenance/run.php /var/www/wiki/mediawiki/extensions/CirrusSearch/maintenance/UpdateSearchIndexConfig.php

# Reindex OpenSearch in MediaWiki
opensearch-reindex:
    #!/usr/bin/env bash
    set -euo pipefail
    just _verify-file compose.yaml
    just _check-containers

    docker compose exec mediawiki php /var/www/wiki/mediawiki/maintenance/run.php /var/www/wiki/mediawiki/extensions/CirrusSearch/maintenance/ForceSearchIndex.php --skipLinks --indexOnSkip
    docker compose exec mediawiki php /var/www/wiki/mediawiki/maintenance/run.php /var/www/wiki/mediawiki/extensions/CirrusSearch/maintenance/ForceSearchIndex.php --skipParse
    docker compose exec mediawiki php /var/www/wiki/mediawiki/maintenance/run.php /var/www/wiki/mediawiki/extensions/CirrusSearch/maintenance/UpdateSuggesterIndex.php

# Run pending jobs in MediaWiki
run-jobs:
    #!/usr/bin/env bash
    set -euo pipefail
    just _verify-file compose.yaml
    just _check-containers

    docker compose exec mediawiki php /var/www/wiki/mediawiki/maintenance/run.php /var/www/wiki/mediawiki/maintenance/runJobs.php

# Update MediaWiki database schema to latest version
mediawiki-schema-update:
    #!/usr/bin/env bash
    set -euo pipefail
    just _destructive-warning "This will update the MediaWiki database schema to the latest version. Ensure you have a backup before proceeding."
    just _verify-file .env
    just _verify-file compose.yaml
    just _check-containers
    ENV=$(just _detect-env)

    # Run database schema updates
    docker compose exec mediawiki php /var/www/wiki/mediawiki/maintenance/run.php /var/www/wiki/mediawiki/maintenance/update.php --quick
    echo "MediaWiki database schema update complete!"
