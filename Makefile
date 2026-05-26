.PHONY: build up down restart shell install update test migrate fresh seed artisan tinker logs

# --- Docker lifecycle ---
build:
	docker compose build --no-cache

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose restart app

logs:
	docker compose logs -f app

# --- App bootstrap (run once after clone) ---
install:
	docker compose run --rm app composer install
	docker compose run --rm app php artisan key:generate
	docker compose run --rm app php artisan migrate --seed

# --- Ongoing development ---
update:
	docker compose run --rm app composer update

test:
	docker compose run --rm app php artisan test

migrate:
	docker compose run --rm app php artisan migrate

fresh:
	docker compose run --rm app php artisan migrate:fresh --seed

seed:
	docker compose run --rm app php artisan db:seed

# make artisan CMD="route:list"
artisan:
	docker compose run --rm app php artisan $(CMD)

tinker:
	docker compose run --rm -it app php artisan tinker

shell:
	docker compose exec app bash
