FROM phpswoole/swoole:4.7-php7.4

#COPY ./rootfilesystem/ /

RUN \
    pecl update-channels        && \
    pecl install redis          && \
    docker-php-ext-enable redis && \
    docker-php-ext-install pcntl