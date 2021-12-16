ARG PROJECT_NAME

FROM ${PROJECT_NAME}_swoole:latest

COPY php.ini ${PHP_INI_DIR}/php.ini

# Install xdebug
RUN pecl install xdebug \
 && docker-php-ext-enable xdebug

# Enable XDebug
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /var/www/sales/www