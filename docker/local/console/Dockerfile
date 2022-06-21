ARG REGISTRY
ARG IMAGE_TAG

FROM ${REGISTRY}crm-php-cli:${IMAGE_TAG}

RUN apt-get update && apt-get install -y openssh-client

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --version=2.0.9 --filename=composer && composer clear-cache

RUN curl -sL https://deb.nodesource.com/setup_17.x | bash
RUN apt-get install --yes nodejs

ARG USER_NAME
ARG USER_ID
ARG USER_GID

RUN groupadd --gid $USER_GID $USER_NAME \
    && useradd --uid $USER_ID --gid $USER_GID -m $USER_NAME

USER ${USER_NAME}