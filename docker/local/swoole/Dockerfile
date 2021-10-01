ARG PROJECT_NAME

FROM ${PROJECT_NAME}_php-fpm:latest

RUN git clone https://github.com/redis/hiredis \
    && cd hiredis/ \
    && git checkout tags/v0.14.1 \
    && make && make install \
    && ldconfig \
    && cd .. \
    && git clone https://github.com/swoole/swoole-src \
    && cd swoole-src/ \
    && git checkout tags/v4.4.16 \
    && phpize && ./configure \
    && make -j 4 && make install \
    && git clone https://github.com/swoole/ext-async \
    && cd ext-async/ \
    && git checkout tags/v4.4.16 \
    && phpize && ./configure \
    && make -j 4 && make install
