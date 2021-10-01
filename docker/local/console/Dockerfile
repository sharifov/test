FROM php:7.4-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
        curl \
        imagemagick \
        unzip \
        git \
    && set -xe \
    && buildDeps=" \
        $PHP_EXTRA_BUILD_DEPS \
        libjpeg62-turbo-dev \
        libpng-dev \
        libmemcached-dev \
        libzip-dev \
        libxml2-dev \
        libmagickwand-dev \
        libpq-dev \
        libonig-dev \
    " \
	&& apt-get update && apt-get install -y --no-install-recommends $buildDeps && rm -rf /var/lib/apt/lists/* \
    && docker-php-source extract \
    && docker-php-ext-configure mysqli --with-mysqli=mysqlnd \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ --enable-gd-jis-conv \
    && docker-php-ext-install exif gd mbstring intl zip mysqli pdo_mysql pdo_pgsql pgsql soap bcmath \
    && echo "UTC" > /etc/timezone && dpkg-reconfigure --frontend noninteractive tzdata \
    && for i in $(seq 1 3); do pecl install -o imagick && s=0 && break || s=$? && sleep 1; done; (exit $s) \
    && docker-php-ext-enable imagick \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

RUN runtimeRequirements="libgeoip-dev" \
    && apt-get update && apt-get install -y ${runtimeRequirements} \
    && pecl install geoip-1.1.1 \
    && echo "extension=geoip.so" > /usr/local/etc/php/conf.d/ext-geoip.ini \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install xdebug
RUN pecl install xdebug \
 && docker-php-ext-enable xdebug

# Enable XDebug
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY php.ini ${PHP_INI_DIR}/php.ini

RUN git clone https://github.com/swoole/swoole-src \
    && cd swoole-src/ \
    && git checkout tags/v4.4.16 \
    && phpize && ./configure \
    && make -j 4 && make install

WORKDIR /var/www/sales/www