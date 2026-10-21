HOST_UID := $(shell id -u)
HOST_GID := $(shell id -g)

export HOST_UID
export HOST_GID

.PHONY: check prepare-env build up down restart logs shell migrate \
        wait-db init-key install-deps clear-cache install permissions

check:
	@command -v docker >/dev/null || (echo "Error: Docker is not installed."; exit 1)
	@docker compose version >/dev/null || (echo "Error: Docker Compose is unavailable."; exit 1)
	@echo "Prerequisites check passed."

prepare-env:
	@if [ ! -f .env ]; then \
		cp .env.example .env && \
		echo "Created .env from .env.example"; \
	else \
		echo ".env already exists; keeping it."; \
	fi

build:
	docker compose build

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose restart

logs:
	docker compose logs -f

shell:
	docker compose exec app sh

migrate:
	docker compose exec app php artisan migrate

wait-db:
	@echo "Waiting for PostgreSQL..."
	@attempt=0; \
	until docker compose exec -T db pg_isready -U tli_user -d tli_market_insights >/dev/null 2>&1; do \
		attempt=$$((attempt + 1)); \
		if [ $$attempt -ge 30 ]; then \
			echo "Error: PostgreSQL did not become ready."; \
			docker compose logs --tail=100 db; \
			exit 1; \
		fi; \
		sleep 2; \
	done
	@echo "PostgreSQL is ready."

init-key:
	@docker compose exec -T app sh -c \
		'grep -q "^APP_KEY=base64:" .env || php artisan key:generate --force'

install-deps:
	docker compose exec -T app composer install --no-interaction --prefer-dist
	@echo "Waiting for frontend dependencies..."
	@attempt=0; \
	until docker compose exec -T vite sh -c 'test -x node_modules/.bin/vite' >/dev/null 2>&1; do \
		attempt=$$((attempt + 1)); \
		if [ $$attempt -ge 60 ]; then \
			echo "Error: Frontend dependencies were not installed."; \
			docker compose logs --tail=100 vite; \
			exit 1; \
		fi; \
		sleep 2; \
	done

clear-cache:
	docker compose exec -T app php artisan optimize:clear

install: check prepare-env
	docker compose up -d --build
	$(MAKE) wait-db
	$(MAKE) install-deps
	$(MAKE) init-key
	$(MAKE) clear-cache
	docker compose exec -T app php artisan migrate --force
	docker compose exec -T app php artisan storage:link
	@echo ""
	@echo "Installation complete!"
	@echo "Application: http://localhost:8000"
	@echo "Vite dev server: http://localhost:5173"

permissions:
	docker compose exec -u root app sh -c \
		'chown -R www-data:www-data storage bootstrap/cache && chmod -R ug+rwX storage bootstrap/cache'
