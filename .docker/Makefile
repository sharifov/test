include .env

down:
	docker-compose down --remove-orphans

stop:
	docker-compose stop

up:
	./initLocalBo.sh
	docker-compose up -d --remove-orphans

restart: stop up

app-console:
	docker-compose run --rm console bash

migrate:
	docker-compose run --rm console ./yii migrate

npm-install:
	docker-compose run --rm console npm install

composer-install:
	docker-compose run --rm console composer install

cs-check:
	docker-compose run --rm console composer cs-check

cs-fix:
	docker-compose run --rm console composer cs-fix2

lint:
	docker-compose run --rm console composer lint

apidoc-gen:
	docker-compose run --rm console npm run apidoc-gen

assets-compressor:
	docker-compose run --rm console composer assets-compressor

build-nginx:
	docker build --build-arg NGINX_VERSION=${NGINX_VERSION} --build-arg USER_NAME=${USER_NAME} --build-arg USER_ID=${USER_ID} --build-arg USER_GID=${USER_GID} --file=nginx/Dockerfile --tag=${REGISTRY}crm-nginx:${IMAGE_TAG} nginx

build-php-fpm:
	docker build --build-arg USER_NAME=${USER_NAME} --build-arg USER_ID=${USER_ID} --build-arg USER_GID=${USER_GID} --build-arg PHP_FPM_VERSION=${PHP_FPM_VERSION} --file=php-fpm/Dockerfile --tag=${REGISTRY}crm-php-fpm:${IMAGE_TAG} php-fpm

build:
	./checkNetworkKivork.sh
	docker build --build-arg PHP_FPM_VERSION=${PHP_FPM_VERSION} --file=php-fpm/Dockerfile --tag=${REGISTRY}crm-php-fpm:${IMAGE_TAG} php-fpm
	docker build --build-arg PHP_CLI_VERSION=${PHP_CLI_VERSION} --file=php-cli/Dockerfile --tag=${REGISTRY}crm-php-cli:${IMAGE_TAG} php-cli
	docker build --build-arg REGISTRY=${REGISTRY} --build-arg IMAGE_TAG=${IMAGE_TAG} --file=swoole/Dockerfile --tag=${REGISTRY}crm-swoole:${IMAGE_TAG} swoole
	docker-compose build

init-config:
	docker run --rm -v ${APP_PATH}:/var/www/html \
	-w=/var/www/html \
	--user ${USER_ID}:${USER_GID} \
	${REGISTRY}crm-php-cli:${IMAGE_TAG} \
	php init --env=Local --overwrite=All \
	&& rm /var/www/html/common/config/supervisor/centrifugo.conf \
	&& rm /var/www/html/common/config/supervisor/socket-server.conf \
	&& cp Makefile.root /var/www/html/Makefile \
	&& cp pre-commit.sh /var/www/html/docker-pre-commit.sh \
	&& chmod +x /var/www/html/docker-pre-commit.sh

install: build init-config
	docker run --rm -v ${APP_PATH}:/var/www/html -v ${SSH_DIR}:/home/${USER_NAME}/.ssh -v ${COMPOSER_CACHE_DIR}:/home/${USER_NAME}/.composer/cache -w=/var/www/html --user ${USER_ID}:${USER_GID} ${COMPOSE_PROJECT_NAME}_console composer install
	docker run --rm -v ${APP_PATH}:/var/www/html -w=/var/www/html --user ${USER_ID}:${USER_GID} ${COMPOSE_PROJECT_NAME}_console npm install