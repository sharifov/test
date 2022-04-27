ARG REGISTRY
ARG IMAGE_TAG

FROM ${REGISTRY}crm-php-fpm:${IMAGE_TAG}

ARG PHP_XDEBUG_ENABLED
ARG XDEBUG_CLIENT_HOST
ARG XDEBUG_CLIENT_PORT
ARG XDEBUG_IDE_KEY
ARG PHP_IDE_CONFIG_API

ENV PHP_IDE_CONFIG=${PHP_IDE_CONFIG_API}

RUN if [ ${PHP_XDEBUG_ENABLED} = true ]; then \
    pecl install xdebug && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=${XDEBUG_CLIENT_HOST}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=${XDEBUG_CLIENT_PORT}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=${XDEBUG_IDE_KEY}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
;fi