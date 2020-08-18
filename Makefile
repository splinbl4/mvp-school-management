docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

management-init: management-composer-install management-wait-db management-migrations manager-fixtures

management-composer-install:
	docker-compose run --rm management-php-cli composer install

management-wait-db:
	until docker-compose exec -T management-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done

management-migrations:
	docker-compose run --rm management-php-cli php bin/console doctrine:migrations:migrate --no-interaction

management-validate-schema:
	docker-compose run --rm management-php-cli php bin/console doctrine:schema:validate

manager-fixtures:
	docker-compose run --rm management-php-cli php bin/console doctrine:fixtures:load --no-interaction