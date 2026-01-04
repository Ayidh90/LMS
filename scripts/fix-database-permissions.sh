#!/bin/bash

# Script to fix database permissions issue
# This script will create the database and grant permissions to the user

set -e

# Load environment variables
if [ -f .env ]; then
    export $(cat .env | grep -v '^#' | xargs)
fi

DB_HOST=${DB_HOST:-db}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-lms_db}
DB_USERNAME=${DB_USERNAME:-lms_user}
DB_PASSWORD=${DB_PASSWORD:-root}
DB_ROOT_PASSWORD=${DB_PASSWORD:-root}

echo "🔧 Fixing database permissions..."
echo "Database: $DB_DATABASE"
echo "User: $DB_USERNAME"
echo ""

# Check if container is running
if ! docker-compose ps db | grep -q "Up"; then
    echo "❌ Database container is not running!"
    echo "Run: docker-compose up -d db"
    exit 1
fi

echo "Creating database if it doesn't exist..."
docker-compose exec -T db mysql -u root -p"$DB_ROOT_PASSWORD" <<EOF
CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON \`$DB_DATABASE\`.* TO '$DB_USERNAME'@'%';
FLUSH PRIVILEGES;
EOF

if [ $? -eq 0 ]; then
    echo "✅ Database permissions fixed!"
    echo ""
    echo "You can now run migrations:"
    echo "  docker-compose exec app php artisan migrate"
else
    echo "❌ Failed to fix database permissions!"
    exit 1
fi

