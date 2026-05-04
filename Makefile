init: docker-down-clear \
api-clear frontend-clear \
docker-pull docker-build docker-up \
api-init frontend-init

up: docker-up
down: docker-down
restart: down up
api-check: validate api-lint api-test
validate: api-validate-schema
lint: api-lint frontend-lint
lint-fix: frontend-lint-fix
api-test: unit-test functional-test api-fixtures
test-unit: unit-test
test-functional: functional-test api-fixtures

docker-up:
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

docker-pull:
	docker compose pull

frontend-init: frontend-yarn-install frontend-ready

frontend-clear:
	docker run --rm -v ${PWD}/frontend:/app -w /app alpine sh -c 'rm -rf .ready dist'

frontend-ready:
	docker run --rm -v ${PWD}/frontend:/app -w /app alpine touch .ready

frontend-yarn-install:
	docker compose run --rm frontend-node-cli yarn install

frontend-lint:
	docker compose run --rm frontend-node-cli yarn eslint
	docker compose run --rm frontend-node-cli yarn stylelint

frontend-lint-fix:
	docker compose run --rm frontend-node-cli yarn eslint-fix
	docker compose run --rm frontend-node-cli yarn stylelint-fix

frontend-test:
	docker compose run --rm frontend-node-cli yarn test

api-init: api-permission composer-install api-wait-for-db api-migrations api-fixtures

api-lint:
	docker compose run --rm api-php-cli composer lint

api-validate-schema:
	docker compose run --rm api-php-cli composer app orm:validate-schema

api-clear:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'rm -rf var/cache/* var/log/* var/test/*'

api-permission:
	docker run --rm -v ${PWD}/api:/app -w /app alpine chmod -R 777 bin var/cache var/log var/test public/templates public/images

composer-install:
	docker compose run --rm -u $$(id -u):$$(id -g) api-php-cli composer install

api-wait-for-db:
	docker compose run --rm api-php-cli wait-for-it mysql:3306 -t 30

api-migrations:
	docker compose run --rm api-php-cli composer app migrations:migrate -- --no-interaction

api-fixtures:
	docker compose run --rm api-php-cli composer app fixtures:load

unit-test:
	docker compose run --rm api-php-cli composer test -- --testsuite=Unit

functional-test:
	docker compose run --rm api-php-cli composer test -- --testsuite=Functional


docker-build:
	docker compose build

build: build-gateway build-frontend build-api

build-gateway:
	docker --log-level=debug build --pull --file=gateway/docker/production/nginx/Dockerfile --tag=${REGISTRY}/safety-docs-gateway:${IMAGE_TAG} gateway

build-frontend:
	docker --log-level=debug build --pull --file=frontend/docker/production/node/Dockerfile --tag=${REGISTRY}/safety-docs-frontend-node:${IMAGE_TAG} frontend


build-api:
	docker --log-level=debug build --pull --file=api/docker/production/nginx/Dockerfile --tag=${REGISTRY}/safety-docs-api:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/production/php-fpm/Dockerfile --tag=${REGISTRY}/safety-docs-api-php-fpm:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/production/php-cli/Dockerfile --tag=${REGISTRY}/safety-docs-api-php-cli:${IMAGE_TAG} api

try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build

push: push-gateway push-frontend push-api

push-gateway:
	docker push ${REGISTRY}/safety-docs-gateway:${IMAGE_TAG}

push-frontend:
	docker push ${REGISTRY}/safety-docs-frontend-node:${IMAGE_TAG}

push-api:
	docker push ${REGISTRY}/safety-docs-api:${IMAGE_TAG}
	docker push ${REGISTRY}/safety-docs-api-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY}/safety-docs-api-php-cli:${IMAGE_TAG}


ifneq ("$(wildcard .env.production)","")
    include .env.production
    export
endif

deploy:
	ssh ${HOST} -p ${PORT} 'rm -rf site_${BUILD_NUMBER}'
	ssh ${HOST} -p ${PORT} 'mkdir site_${BUILD_NUMBER}'

	scp -P ${PORT} docker-compose-production.yml ${HOST}:site_${BUILD_NUMBER}/docker-compose.yml

	ssh ${HOST} -p ${PORT} 'echo "${REGISTRY_PASSWORD}" | docker login ${REGISTRY} -u "${REGISTRY_USER}" --password-stdin'

	envsubst < .env.template > .env.local
	scp -P ${PORT} .env.local ${HOST}:site_${BUILD_NUMBER}/.env
	rm .env.local

	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose pull'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose up --build --remove-orphans -d'

	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose run --rm api-php-cli wait-for-it mysql:3306 -t 60'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose run --rm api-php-cli composer app migrations:migrate -- --no-interaction'

	ssh ${HOST} -p ${PORT} 'rm -f site'
	ssh ${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'
	ssh ${HOST} -p ${PORT} 'docker image prune -af'
	ssh ${HOST} -p ${PORT} 'cd /home/deploy && ls -dt site_* | tail -n +4 | xargs rm -rf'


rollback:
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose pull'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker compose up --build --remove-orphans -d'
	ssh ${HOST} -p ${PORT} 'rm -f site'
	ssh ${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'
