ARG REGISTRY
ARG PHP_CLI_IMAGE_TAG

FROM ${REGISTRY}crm-php-cli:${PHP_CLI_IMAGE_TAG}

RUN git clone https://github.com/redis/hiredis \
    && cd hiredis/ \
    && git checkout tags/v1.0.2 \
    && make -j && make install \
    && ldconfig \
    && cd .. \
    && git clone https://github.com/swoole/swoole-src \
    && cd swoole-src/ \
    && git checkout tags/v4.8.9 \
    && phpize && ./configure \
    && make -j 4 && make install

COPY conf.d /usr/local/etc/php/conf.d