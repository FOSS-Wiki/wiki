# Start all wiki containers
start:
    #!/usr/bin/env bash
    set -euo pipefail
    JUSTFILE_DIR="{{justfile_directory()}}"
    just _verify-file .env

    cd "$JUSTFILE_DIR" && docker compose up -d --build

# Stop all running wiki containers
stop:
    #!/usr/bin/env bash
    set -euo pipefail
    JUSTFILE_DIR="{{justfile_directory()}}"
    just _check-containers

    cd "$JUSTFILE_DIR" && docker compose down

# Restart all wiki containers
restart:
    #!/usr/bin/env bash
    set -euo pipefail
    just _check-containers

    echo "Stopping wiki containers..."
    just stop
    echo "Starting wiki containers..."
    just start

# Pull latest code, build while running, then swap containers
update:
    #!/usr/bin/env bash
    set -euo pipefail
    JUSTFILE_DIR="{{justfile_directory()}}"
    just _verify-file .env
    just _destructive-warning "This will pull the latest code, check the changelog before proceeding."
    just _check-sudo-dir

    cd "$JUSTFILE_DIR"

    echo "Pulling latest code from git..."
    git pull

    echo "Building new images..."
    docker compose build --pull

    echo "Resetting permissions"
    just fix-perms

    echo "Recreating updated containers"
    docker compose up -d --remove-orphans

    # Ensures containers have started before running tasks
    echo "Applying startup tasks..."
    for i in {1..60}; do
        if just _containers-running; then break; fi
        sleep 2
    done
    sleep 5
    just startup-tasks
    echo "Update complete!"
