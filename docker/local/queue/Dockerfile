ARG PROJECT_NAME

FROM ${PROJECT_NAME}_swoole:latest

COPY php.ini ${PHP_INI_DIR}/php.ini

RUN apt-get update && apt-get install -y supervisor \
    && mkdir -p /var/log/supervisor \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ARG APP_ROOT

COPY supervisord.conf /etc/supervisor/supervisord.conf
RUN echo "files = $APP_ROOT/www/common/config/supervisor/*.conf" >> /etc/supervisor/supervisord.conf

WORKDIR /var/www/sales/www

CMD ["/usr/bin/supervisord"]