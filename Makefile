init: docker-down-clear frontend-clear docker-pull docker-build docker-up frontend-init
up: docker-up
down: docker-down
restart: down up
lint: frontend-lint
lint-fix: frontend-lint-fix
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



