#!/bin/bash

set -e

echo "🚀 Setting up Laravel LMS with Inertia.js..."

# Copy .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
fi

# Start Docker containers
echo "🐳 Starting Docker containers..."
docker-compose up -d

# Wait for database to be ready
echo "⏳ Waiting for database to be ready..."
sleep 10

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
docker-compose exec -T app composer install --no-interaction

# Generate application key
echo "🔑 Generating application key..."
docker-compose exec -T app php artisan key:generate --force

# Install Node dependencies
echo "📦 Installing Node dependencies..."
docker-compose exec -T node npm install

# Run migrations
echo "🗄️  Running database migrations..."
docker-compose exec -T app php artisan migrate --force

# Seed database
echo "🌱 Seeding database..."
docker-compose exec -T app php artisan db:seed --force

echo "✅ Setup complete!"
echo ""
# Load environment variables
if [ -f .env ]; then
    export $(cat .env | grep -v '^#' | xargs)
fi

APP_PORT=${APP_PORT:-8000}
DB_EXTERNAL_PORT=${DB_EXTERNAL_PORT:-8280}
NODE_PORT=${NODE_PORT:-5173}

echo "🌐 Application: http://localhost:${APP_PORT}"
echo "📊 Database: localhost:${DB_EXTERNAL_PORT}"
echo "⚡ Vite Dev Server: http://localhost:${NODE_PORT}"
echo ""
echo "📝 Default Login Credentials:"
echo "   Admin: admin@lms.com / password"
echo "   Instructor: instructor@lms.com / password"
echo "   Student: student@lms.com / password"
echo ""
echo "To stop containers: docker-compose down"
echo "To view logs: docker-compose logs -f"

