up: docker-up
down: docker-down
restart: docker-down docker-up

init: docker-down-clear docker-pull docker-build docker-up composer-install

migration_fixtures: migrations-install fixtures-load

test:
	docker-compose run --rm php-cli php bin/phpunit

composer-install:
	docker-compose run --rm php-cli composer install

migrations-install:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:migrate --no-interaction

migrations-diff:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:diff

fixtures-load:
	docker-compose run --rm php-cli php bin/console doctrine:fixtures:load --no-interaction

schema-validate:
	docker-compose run --rm php-cli php bin/console doctrine:schema:validate

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build


recreate-test-database:
	$(info ************  Start drop & create new database for test env ************)
	docker-compose run --rm php-cli php bin/console doctrine:database:drop --env=test --force
	docker-compose run --rm php-cli php bin/console doctrine:database:create --env=test
	docker-compose run --rm php-cli php bin/console doctrine:migration:migrate --env=test --no-interaction