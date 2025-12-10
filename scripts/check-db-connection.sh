#!/bin/bash

# Script to check database connection

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

echo "🔍 Checking database connection..."
echo "Host: $DB_HOST"
echo "Port: $DB_PORT"
echo "Database: $DB_DATABASE"
echo "Username: $DB_USERNAME"
echo ""

# Check if container is running
if ! docker-compose ps db | grep -q "Up"; then
    echo "❌ Database container is not running!"
    echo "Run: docker-compose up -d db"
    exit 1
fi

# Try to connect
echo "Testing connection..."
docker-compose exec -T db mysql -h localhost -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "USE $DB_DATABASE; SELECT 1;" > /dev/null 2>&1

if [ $? -eq 0 ]; then
    echo "✅ Database connection successful!"
else
    echo "❌ Database connection failed!"
    echo "Check your credentials in .env file"
    exit 1
fi

