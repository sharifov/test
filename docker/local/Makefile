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

build:
	./checkNetworkKivork.sh
	docker build --build-arg PHP_FPM_VERSION=${PHP_FPM_VERSION}  \
	    --build-arg USER_NAME=${USER_NAME} \
	    --build-arg USER_ID=${USER_ID}  \
	    --build-arg USER_GID=${USER_GID} \
	    --file=php-fpm/Dockerfile --tag=${REGISTRY}crm-php-fpm:${IMAGE_TAG} php-fpm
	docker build --build-arg PHP_CLI_VERSION=${PHP_CLI_VERSION} --file=php-cli/Dockerfile --tag=${REGISTRY}crm-php-cli:${IMAGE_TAG} php-cli
	docker-compose build

init-config:
	docker run --rm -v ${APP_PATH}:${APP_PATH} \
	-w=${APP_PATH} \
	--user ${USER_ID}:${USER_GID} \
	${REGISTRY}crm-php-cli:${IMAGE_TAG} \
	php init --env=Local --overwrite=All \
	&& rm ${APP_PATH}/common/config/supervisor/centrifugo.conf \
	&& rm ${APP_PATH}/common/config/supervisor/socket-server.conf \
	&& cp Makefile.root ${APP_PATH}/Makefile \
	&& cp pre-commit.sh ${APP_PATH}/docker-pre-commit.sh \
	&& chmod +x ${APP_PATH}/docker-pre-commit.sh

install: build init-config
	docker run --rm -v ${APP_PATH}:${APP_PATH} -v ${SSH_DIR}:/home/${USER_NAME}/.ssh -v ${COMPOSER_CACHE_DIR}:/home/${USER_NAME}/.composer/cache -w=${APP_PATH} --user ${USER_ID}:${USER_GID} ${COMPOSE_PROJECT_NAME}_console composer install
	docker run --rm -v ${APP_PATH}:${APP_PATH} -w=${APP_PATH} --user ${USER_ID}:${USER_GID} ${COMPOSE_PROJECT_NAME}_console npm install