.PHONY: up down build install migrate fixtures setup logs

up:
	docker compose up -d --build

down:
	docker compose down

build:
	docker compose build

install:
	docker compose run --rm php composer install --no-interaction

migrate:
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	docker compose exec php php bin/console doctrine:fixtures:load --no-interaction

setup: up install migrate fixtures
	@echo "API disponible sur http://localhost:8081/api"
	@echo "Documentation Swagger sur http://localhost:8081/api/docs"

logs:
	docker compose logs -f
