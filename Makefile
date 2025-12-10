.PHONY: help build up down restart shell composer npm migrate seed test

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

build: ## Build Docker containers
	docker-compose build

up: ## Start Docker containers
	docker-compose up -d

down: ## Stop Docker containers
	docker-compose down

restart: ## Restart Docker containers
	docker-compose restart

shell: ## Access app container shell
	docker-compose exec app bash

composer: ## Run composer install
	docker-compose exec app composer install

npm: ## Run npm install
	docker-compose exec node npm install

migrate: ## Run database migrations
	docker-compose exec app php artisan migrate

seed: ## Run database seeders
	docker-compose exec app php artisan db:seed --force

test: ## Run tests
	docker-compose exec app php artisan test

setup: ## Initial project setup
	@if [ ! -f .env ]; then \
		cp .env.example .env; \
		echo "✅ Created .env file"; \
	fi
	docker-compose up -d
	sleep 10
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec node npm install
	docker-compose exec app php artisan migrate --force
	docker-compose exec app php artisan db:seed --force
	@echo "Setup complete!"
	@echo "Application: http://localhost:$$(grep APP_PORT .env 2>/dev/null | cut -d '=' -f2 || echo '8000')"
	@echo "Database: localhost:$$(grep DB_EXTERNAL_PORT .env 2>/dev/null | cut -d '=' -f2 || echo '8280')"
	@echo "Default users: admin@lms.com / password"

env: ## Create .env file from .env.example
	@if [ -f .env ]; then \
		echo "⚠️  .env already exists. Use 'make env-force' to overwrite."; \
	else \
		cp .env.example .env; \
		echo "✅ Created .env file"; \
	fi

env-force: ## Force create .env file (overwrites existing)
	cp .env.example .env
	@echo "✅ Created .env file (overwritten)"

env-check: ## Check environment configuration
	@echo "📋 Environment Configuration:"
	@echo "================================"
	@if [ -f .env ]; then \
		echo "✅ .env file exists"; \
		grep -E "^(APP_|DB_|DOCKER_|APP_PORT|DB_EXTERNAL_PORT|NODE_PORT)" .env | grep -v "^#" || true; \
	else \
		echo "❌ .env file not found. Run 'make env' to create it."; \
	fi

