#!/bin/bash
set -e

# Copy public assets to the shared volume
# This ensures that Nginx (which mounts this volume) has access to the latest built assets
if [ -d "/var/www/html/public" ] && [ -d "/var/www/html/public_shared" ]; then
    echo "Syncing public assets to shared volume..."
    cp -rf /var/www/html/public/. /var/www/html/public_shared/
fi

# Execute the passed command
exec "$@"
