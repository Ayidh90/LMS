#!/bin/bash
set -e

# Check if we are running as root
if [ "$(id -u)" = "0" ]; then
    # We are root (Production scenario)
    
    # Copy public assets to the shared volume
    if [ -d "/var/www/html/public" ] && [ -d "/var/www/html/public_shared" ]; then
        echo "Syncing public assets to shared volume..."
        cp -rf /var/www/html/public/. /var/www/html/public_shared/
        
        # After copying, ensure the files are owned by www-data
        chown -R www-data:www-data /var/www/html/public_shared
    fi

    # Switch to www-data user to run the main command safely
    exec runuser -u www-data -- "$@"
else
    # We are not root (Local Dev scenario where user is set in docker-compose)
    # Just execute the command directly
    exec "$@"
fi
