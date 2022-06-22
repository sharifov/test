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

ARG USER_NAME
ARG USER_ID
ARG USER_GID

RUN groupadd --gid $USER_GID $USER_NAME \
    && useradd --uid $USER_ID --gid $USER_GID -m $USER_NAME

USER ${USER_NAME}