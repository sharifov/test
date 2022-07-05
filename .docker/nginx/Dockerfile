ARG NGINX_VERSION

FROM nginx:$NGINX_VERSION

ENV NGINX_ENVSUBST_TEMPLATE_SUFFIX=.conf

ARG USER_NAME
ARG USER_ID
ARG USER_GID

RUN groupadd --gid $USER_GID $USER_NAME \
    && useradd --uid $USER_ID --gid $USER_GID -m $USER_NAME \
    && mkdir -p /var/run/nginx /var/tmp/nginx \
    && chown -R $USER_NAME:$USER_NAME /etc/nginx/conf.d /usr/share/nginx /var/run/nginx /var/tmp/nginx

COPY nginx.conf /etc/nginx/nginx.conf

USER $USER_NAME