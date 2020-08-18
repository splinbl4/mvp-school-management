init: docker-down-clear management-clear docker-up management-init

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

management-init: management-composer-install management-assets-install management-wait-db management-migrations management-fixtures management-ready

management-clear:
	docker run --rm -v ${PWD}/management:/app --workdir=/app alpine rm -f .ready

management-composer-install:
	docker-compose run --rm management-php-cli composer install

management-assets-install:
	docker-compose run --rm management-node yarn install

management-wait-db:
	until docker-compose exec -T management-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done

management-migrations:
	docker-compose run --rm management-php-cli php bin/console doctrine:migrations:migrate --no-interaction

management-validate-schema:
	docker-compose run --rm management-php-cli php bin/console doctrine:schema:validate

management-fixtures:
	docker-compose run --rm management-php-cli php bin/console doctrine:fixtures:load --no-interaction

management-ready:
	docker run --rm -v ${PWD}/management:/app --workdir=/app alpine touch .ready

management-assets-dev:
	docker-compose run --rm management-node npm run dev

management-test:
	docker-compose run --rm management-php-cli php bin/phpunit