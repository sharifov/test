ARG REGISTRY
ARG SWOOLE_IMAGE_TAG

FROM ${REGISTRY}crm-swoole:${SWOOLE_IMAGE_TAG}

RUN apt-get update && apt-get install -y supervisor \
    && mkdir -p /var/log/supervisor \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY supervisord.conf /etc/supervisor/supervisord.conf

RUN echo "files = /var/www/app/common/config/supervisor/*.conf" >> /etc/supervisor/supervisord.conf

CMD ["/usr/bin/supervisord"]