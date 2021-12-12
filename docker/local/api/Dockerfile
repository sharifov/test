ARG PROJECT_NAME

FROM ${PROJECT_NAME}_php-fpm:latest

COPY php.ini ${PHP_INI_DIR}/php.ini

RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /var/www/sales/www