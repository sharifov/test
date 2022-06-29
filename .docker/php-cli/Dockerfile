ARG PHP_CLI_VERSION

FROM php:${PHP_CLI_VERSION}-cli

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin

RUN install-php-extensions imagick \
    intl \
    zip \
    gd \
    mysqli \
    pdo_mysql \
    pgsql \
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

RUN apt-get update && apt-get install -y \
    git \
    && rm -rf /var/lib/apt/lists/*