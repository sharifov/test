ARG NGINX_VERSION

FROM nginx:$NGINX_VERSION

ENV NGINX_ENVSUBST_TEMPLATE_SUFFIX=.conf

ARG USER_NAME
ARG USER_ID
ARG USER_GID

RUN mkdir -p /var/run/nginx /var/tmp/nginx

RUN set -x \
    && groupmod -g "${USER_GID}" nginx \
    && usermod -u "${USER_ID}" -g "${USER_GID}" nginx


COPY nginx.conf /etc/nginx/nginx.conf