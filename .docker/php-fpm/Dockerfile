ARG PHP_FPM_VERSION

FROM php:${PHP_FPM_VERSION}-fpm

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin

RUN install-php-extensions imagick \
    intl \
    zip \
    gd \
    pdo_mysql \
    pdo_pgsql \
    bcmath \
    opcache \
    soap \
    xmlrpc \
    tidy \
    imap \
    pspell \
    ssh2 \
    redis

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY conf.d /usr/local/etc/php/conf.d

ARG PHP_XDEBUG_ENABLED
ARG XDEBUG_CLIENT_HOST
ARG XDEBUG_CLIENT_PORT
ARG XDEBUG_IDE_KEY

RUN if [ ${PHP_XDEBUG_ENABLED} = true ]; then \
    pecl install xdebug && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=${XDEBUG_CLIENT_HOST}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=${XDEBUG_CLIENT_PORT}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=${XDEBUG_IDE_KEY}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
;fi

ARG USER_NAME
ARG USER_ID
ARG USER_GID

RUN groupadd --gid $USER_GID $USER_NAME \
    && useradd --uid $USER_ID --gid $USER_GID -m $USER_NAME

USER ${USER_NAME}