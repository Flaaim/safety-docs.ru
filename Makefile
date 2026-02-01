init: docker-down-clear \
api-clear frontend-clear \
docker-pull docker-build docker-up \
api-init frontend-init

up: docker-up
down: docker-down
restart: down up
lint: frontend-lint
lint-fix: frontend-lint-fix
api-test: unit-test functional-test api-fixtures
test-unit: unit-test
test-functional: functional-test api-fixtures

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

frontend-init: frontend-yarn-install frontend-ready

frontend-clear:
	docker run --rm -v ${PWD}/frontend:/app -w /app alpine sh -c 'rm -rf .ready dist'

frontend-ready:
	docker run --rm -v ${PWD}/frontend:/app -w /app alpine touch .ready

frontend-yarn-install:
	docker-compose run --rm frontend-node-cli yarn install

frontend-lint:
	docker-compose run --rm frontend-node-cli yarn eslint
	docker-compose run --rm frontend-node-cli yarn stylelint

frontend-lint-fix:
	docker-compose run --rm frontend-node-cli yarn eslint-fix
	docker-compose run --rm frontend-node-cli yarn stylelint-fix

frontend-test:
	docker compose run --rm frontend-node-cli yarn test

api-init: api-permission composer-install api-wait-for-db api-migrations api-fixtures

api-clear:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'rm -rf var/cache/* var/log/* var/test/*'

api-permission:
	docker run --rm -v ${PWD}/api:/app -w /app alpine chmod 777 bin var/cache var/log public/templates

composer-install:
	docker-compose run --rm api-php-cli composer install

api-wait-for-db:
	docker-compose run --rm api-php-cli wait-for-it mysql:3306 -t 30

api-migrations:
	docker-compose run --rm api-php-cli composer app migrations:migrate -- --no-interaction

api-fixtures:
	docker-compose run --rm api-php-cli composer app fixtures:load

unit-test:
	docker-compose run --rm api-php-cli composer test -- --testsuite=Unit

functional-test:
	docker-compose run --rm api-php-cli composer test -- --testsuite=Functional
