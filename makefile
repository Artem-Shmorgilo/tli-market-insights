export WWWUSER := $(shell id -u)
export WWWGROUP := $(shell id -g)

SAIL := ./vendor/bin/sail

.PHONY: install check prepare-env up down restart build logs shell \
        migrate fresh test npm-dev npm-build clear-cache wait-db

# Check the tools needed to bootstrap the project
check:
	@command -v docker >/dev/null || (echo "Error: Docker is required."; exit 1)
	@docker compose version >/dev/null || (echo "Error: Docker Compose is required."; exit 1)
	@command -v composer >/dev/null || (echo "Error: Composer is required for initial setup."; exit 1)
	@echo "Prerequisites check passed."

# Create local environment configuration without overwriting existing settings
prepare-env:
	@if [ ! -f .env ]; then \
		cp .env.example .env || exit 1; \
		echo "Created .env from .env.example"; \
	else \
		echo ".env already exists; keeping it."; \
	fi

# Full initial setup for a new developer
install: check prepare-env
	composer install --no-interaction --prefer-dist
	@$(SAIL) up -d --build
	@$(MAKE) wait-db
	@$(SAIL) npm install
	@if ! grep -q '^APP_KEY=base64:' .env; then \
		$(SAIL) artisan key:generate; \
	fi
	@$(SAIL) artisan optimize:clear
	@$(SAIL) artisan migrate --force
	@$(SAIL) artisan db:seed
	@$(SAIL) artisan storage:link
	@echo ""
	@echo "Installation complete!"
	@echo "Application: http://localhost:$${APP_PORT:-8000}"
	@echo "Run 'make npm-dev' to start the Vite development server."

# Start existing containers
up:
	@$(SAIL) up -d

# Stop containers without deleting database data
down:
	@$(SAIL) down

restart:
	@$(SAIL) restart

build:
	@$(SAIL) build --no-cache

logs:
	@$(SAIL) logs -f

shell:
	@$(SAIL) shell

migrate:
	@$(SAIL) artisan migrate

# Destructive: drops all tables and reruns migrations
fresh:
	@$(SAIL) artisan migrate:fresh

test:
	@$(SAIL) artisan test

npm-dev:
	@$(SAIL) npm run dev -- --host 0.0.0.0

npm-build:
	@$(SAIL) npm run build

clear-cache:
	@$(SAIL) artisan optimize:clear

# Wait until PostgreSQL accepts connections
wait-db:
	@echo "Waiting for PostgreSQL..."
	@attempt=0; \
	until docker compose exec -T pgsql sh -c \
		'pg_isready -q -U "$$POSTGRES_USER" -d "$$POSTGRES_DB"' >/dev/null 2>&1; do \
		attempt=$$((attempt + 1)); \
		if [ $$attempt -ge 30 ]; then \
			echo "Error: PostgreSQL did not become ready."; \
			docker compose logs --tail=100 pgsql; \
			exit 1; \
		fi; \
		sleep 2; \
	done
	@echo "PostgreSQL is ready."
