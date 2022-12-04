GROUP = auth
GROUP = User

setup:
	cp .env.example .env && cp laravel/.env.example .env
	docker compose run --rm api sh -c "composer install && php artisan key:generate"
	docker compose run --rm api chmod -R a+rw storage/ bootstrap/cache
	docker compose up -d

start:
	docker compose start

exec:
	docker compose exec app ash

stop:
	docker compose stop

phpstan:
	docker compose exec app ash -c "composer phpstan"

test:
	cp ./openapi/openapi.develop.yaml ./laravel/openapi.yaml
	docker compose exec app ash -c "make test GROUP=${GROUP}"

test-all:
	cp ./openapi/openapi.develop.yaml ./laravel/openapi.yaml
	docker compose exec app ash -c "make test-all"

crud:
	docker compose exec app ash -c "php artisan initial:api ${MODEL}"
