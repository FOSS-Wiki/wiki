# Verify that a file exists, exit with error if not found
_verify-file file:
    #!/usr/bin/env bash
    set -euo pipefail
    JUSTFILE_DIR="{{justfile_directory()}}"
    FILE_PATH="$JUSTFILE_DIR/{{file}}"

    echo "Verifying file: {{file}}"
    if [ ! -f "$FILE_PATH" ]; then
        echo -e "\n\033[1;37m\033[41m ERROR \033[0m \033[1;31mFile '{{file}}' not found\033[0m\n"
        exit 1
    fi

# Detect the environment (local/staging/production) from the filename
_detect-env:
    #!/usr/bin/env bash
    set -euo pipefail
    JUSTFILE_DIR="{{justfile_directory()}}"
    compose_file=$(grep -m 1 "^COMPOSE_FILE=" "$JUSTFILE_DIR/.env" | cut -d'=' -f2- | tr -d ' "')

    IFS=':' read -ra FILES <<< "$compose_file"
    env=""
    for f in "${FILES[@]}"; do
        case "$f" in
            compose.production.yaml) env="production" ;;
            compose.staging.yaml)    env="staging" ;;
            compose.local.yaml)      env="local" ;;
        esac
    done

    if [ -z "$env" ]; then
        echo -e "\n\033[1;37m\033[41m ERROR \033[0m \033[1;31mCould not detect environment from COMPOSE_FILE in .env\033[0m\n"
        exit 1
    fi

    echo "$env"

# Check if containers for the detected environment are running
_check-containers:
    #!/usr/bin/env bash
    set -euo pipefail
    ENV=$(just _detect-env)
    echo "Environment detected: $ENV"

    # Count running containers matching the environment prefix
    echo "Checking for running containers..."
    CONTAINER_COUNT=$(docker compose ps --format "{{{{.Name}}}}\t{{{{.Status}}}}" | wc -l)
    if [ "$CONTAINER_COUNT" -eq 0 ]; then
        echo -e "\n\033[1;37m\033[41m ERROR \033[0m \033[1;31mNo running containers found for '$ENV'. Please start the services first.\033[0m\n"
        exit 1
    fi
    echo "Found $CONTAINER_COUNT running container(s)"

# Copy a file from source to destination with verification
_copy-file src dst:
    #!/usr/bin/env bash
    set -euo pipefail
    JUSTFILE_DIR="{{justfile_directory()}}"

    SRC="$JUSTFILE_DIR/{{src}}"
    DST="$JUSTFILE_DIR/{{dst}}"
    just _destructive-warning "You are about to overwrite {{dst}} which may overwrite existing data in that file."

    echo "Copying file: {{src}} → {{dst}}"
    if [ ! -f "$SRC" ]; then
        echo -e "\n\033[1;37m\033[41m ERROR \033[0m \033[1;31mSource '{{src}}' not found\033[0m\n" >&2
        exit 1
    fi

    cp -v "$SRC" "$DST"
    echo "Successfully copied {{src}} to {{dst}}"

# Check if the script is running with sudo/root privileges
_check-sudo:
    #!/usr/bin/env bash
    set -euo pipefail

    if [ "$EUID" -ne 0 ]; then
        echo -e "\n\033[1;37m\033[41m ERROR \033[0m \033[1;31mThis script must be run with 'sudo'.\033[0m\n"
        exit 1
    fi

# Check if current directory is owned by root and require sudo if so
_check-sudo-dir:
    #!/usr/bin/env bash
    set -euo pipefail

    # Get directory owner
    OWNER=$(stat -c '%U' .)

    if [ "$OWNER" = "root" ] && [ "$EUID" -ne 0 ]; then
        echo -e "\n\033[1;37m\033[41m ERROR \033[0m \033[1;31mCurrent directory is owned by root. Please run with 'sudo'.\033[0m\n"
        exit 1
    fi

# Display a warning for destructive actions and prompt for confirmation
_destructive-warning reason="":
    #!/usr/bin/env bash
    set -euo pipefail

    echo -e "\n\033[1;37m\033[41m WARNING \033[0m \033[1;33mThis action is destructive. Proceed with caution.\033[0m"

    if [ -n "{{reason}}" ]; then
        echo -e "          \033[3;38;5;208m{{reason}}\033[0m"
    fi

    echo -e "\033[1;33mPlease check the help command or documentation for this script before proceeding to understand the consequences.\033[0m\n"
    read -p "Do you want to continue? (y/n): " -r REPLY

    # Normalize to lowercase for comparison
    REPLY_LOWER=$(echo "$REPLY" | tr '[:upper:]' '[:lower:]')
    if [[ ! "$REPLY_LOWER" =~ ^(y|yes)$ ]]; then
        echo "Aborting."
        exit 1
    fi

_containers-running:
    #!/usr/bin/env bash
    set -euo pipefail
    CONTAINER_COUNT=$(docker compose ps --format "{{{{.Name}}}}\t{{{{.Status}}}}" | wc -l)
    [ "$CONTAINER_COUNT" -gt 0 ]
