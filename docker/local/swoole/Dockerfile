ARG REGISTRY
ARG IMAGE_TAG

FROM ${REGISTRY}crm-php-cli:${IMAGE_TAG}

RUN git clone https://github.com/redis/hiredis \
    && cd hiredis/ \
    && git checkout tags/v1.0.2 \
    && make && make install \
    && ldconfig \
    && cd .. \
    && git clone https://github.com/swoole/swoole-src \
    && cd swoole-src/ \
    && git checkout tags/v4.8.9 \
    && phpize && ./configure \
    && make -j 4 && make install \
    && cd ..
#    && git clone https://github.com/swoole/ext-async \
#    && cd ext-async/ \
#    && git checkout tags/v4.4.16 \
#    && phpize && ./configure \
#    && make -j 4 && make install

COPY conf.d /usr/local/etc/php/conf.d