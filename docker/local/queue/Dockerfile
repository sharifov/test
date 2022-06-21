ARG REGISTRY
ARG IMAGE_TAG

FROM ${REGISTRY}crm-php-cli:${IMAGE_TAG}

RUN apt-get update && apt-get install -y supervisor \
    && mkdir -p /var/log/supervisor \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ARG APP_PATH

COPY supervisord.conf /etc/supervisor/supervisord.conf
RUN echo "files = $APP_PATH/common/config/supervisor/*.conf" >> /etc/supervisor/supervisord.conf

CMD ["/usr/bin/supervisord"]