ARG PHP_FPM_VERSION

FROM php:${PHP_FPM_VERSION}-fpm

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

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY conf.d /usr/local/etc/php/conf.d

ARG USER_NAME
ARG USER_ID
ARG USER_GID

RUN groupadd --gid $USER_GID $USER_NAME \
    && useradd --uid $USER_ID --gid $USER_GID -m $USER_NAME

USER ${USER_NAME}

ARG SSH_KEY
ARG SSH_KEY_PUB

RUN mkdir -p /home/"${USER_NAME}"/.ssh && \
    chmod 0700 /home/"${USER_NAME}"/.ssh && \
    ssh-keyscan bitbucket.org > /home/"${USER_NAME}"/.ssh/known_hosts && \
    echo "${SSH_KEY}" > /home/"${USER_NAME}"/.ssh/id_rsa && \
    echo "${SSH_KEY_PUB}" > /home/"${USER_NAME}"/.ssh/id_rsa.pub && \
    chmod 600 /home/"${USER_NAME}"/.ssh/id_rsa && \
    printf "Host bitbucket.org\nIdentityFile /home/${USER_NAME}/.ssh/id_rsa\n" > /home/"${USER_NAME}"/.ssh/config && \
    chmod 600 /home/"${USER_NAME}"/.ssh/config && \
    chown -R "${USER_ID}":"${GROUP_ID}" /home/"${USER_NAME}"/