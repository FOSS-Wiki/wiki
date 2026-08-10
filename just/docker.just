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

# Restart all wiki containers and removes cache volumes
clean-restart:
    #!/usr/bin/env bash
    set -euo pipefail
    just _check-containers

    echo "Stopping wiki containers and removing cache volumes..."
    just clean-stop
    echo "Starting wiki containers..."
    just start

# Stop all wiki containers and removes cache volumes
clean-stop:
    #!/usr/bin/env bash
    set -euo pipefail
    JUSTFILE_DIR="{{justfile_directory()}}"
    just _check-containers

    cd "$JUSTFILE_DIR" && docker compose down -v

# Pull latest code and restart wiki containers
update:
    #!/usr/bin/env bash
    set -euo pipefail
    just _verify-file .env
    just _destructive-warning "This will pull the latest code, check the changelog before proceeding."
    just _check-sudo-dir

    # Stop if containers are running
    if just _containers-running ; then
        echo "Stopping wiki containers..."
        just clean-stop
    fi

    # Pull updates
    echo "Pulling latest code from git..."
    git pull

    echo "Resetting permissions"
    just fix-perms
    echo "Restarting wiki containers..."
    just start
    echo "Applying startup tasks..."

    # Wait for containers to be fully up before running startup tasks
    sleep 30
    just startup-tasks
    echo "Update complete!"
