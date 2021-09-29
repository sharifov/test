ARG PHP_VERSION

FROM yiisoftware/yii2-php:${PHP_VERSION}

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && \
    apt-get -y install \
            libxslt1-dev \
            libtidy-dev \
            libc-client-dev \
            libkrb5-dev \
            libpspell-dev \
#            supervisor \
            nginx \
            vim \
            nano \
            mc \
            wget \
            git \
            redis-server \
        --no-install-recommends && \
        apt-get clean && \
        rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

RUN wget https://github.com/redis/hiredis/archive/v0.14.1.tar.gz &&  \
    tar zxvf v0.14.1.tar.gz
    RUN cd hiredis-0.14.1/ && \
    make -j && \
    make install

RUN ldconfig
RUN cd ..

RUN git clone https://github.com/swoole/swoole-src
RUN cd swoole-src/ && \
    git checkout tags/v4.4.16 && \
    phpize && ./configure && \
    make -j 4 && make install

RUN git clone https://github.com/swoole/ext-async
RUN cd ext-async/ && \
    git checkout tags/v4.4.16 && \
    phpize && ./configure && \
    make -j 4 && make install


RUN touch /usr/local/etc/php/conf.d/swoole.ini && \
    echo "extension=swoole.so\nextension=swoole_async.so" > /usr/local/etc/php/conf.d/swoole.ini


CMD ["./yii websocket-server/start"]

EXPOSE 8080

#EXPOSE 9501 8080

#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer



#RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
#&& curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
## Make sure we're installing what we think we're installing!
#&& php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
#&& php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot \
#&& rm -f /tmp/composer-setup.*

#WORKDIR /app
#CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]


#RUN composer install --no-dev
# use --env=Development both with not use gii and debug modules
#RUN cp //environments/prod/console/config/main-local.php /var/www/html/environments/dev/console/config/main-local.php
#RUN cp /var/www/html/environments/prod/frontend/config/main-local.php /var/www/html/environments/dev/frontend/config/main-local.php
#RUN cp /var/www/html/environments/prod/backend/config/main-local.php /var/www/html/environments/dev/backend/config/main-local.php
#RUN ./init --env=Local --overwrite=All