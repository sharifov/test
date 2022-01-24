include .env

down:
	docker-compose down --remove-orphans

up:
	docker-compose up

app-console:
	docker-compose run --rm console bash

migrate:
	docker-compose run --rm console ./yii migrate

build:
	docker build --build-arg PHP_FPM_VERSION=${PHP_FPM_VERSION} --file=php-fpm/Dockerfile --tag=${REGISTRY}crm-php-fpm:${IMAGE_TAG} php-fpm
	docker build --build-arg PHP_CLI_VERSION=${PHP_CLI_VERSION} --file=php-cli/Dockerfile --tag=${REGISTRY}crm-php-cli:${IMAGE_TAG} php-cli
	docker build --build-arg REGISTRY=${REGISTRY} --build-arg IMAGE_TAG=${IMAGE_TAG} --file=swoole/Dockerfile --tag=${REGISTRY}crm-swoole:${IMAGE_TAG} swoole
	docker-compose build

init-config:
	docker run --rm -v ${APP_PATH}:${APP_PATH} \
	-w=${APP_PATH} \
	--user ${USER_ID}:${USER_GID} \
	${REGISTRY}crm-php-cli:${IMAGE_TAG} \
	php init --env=Local --overwrite=All