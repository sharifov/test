ARG REGISTRY
ARG PHP_CLI_IMAGE_TAG

FROM ${REGISTRY}crm-php-cli:${PHP_CLI_IMAGE_TAG}

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

ARG SSH_KEY
ARG SSH_KEY_PUB

RUN mkdir -p /home/"${USER_NAME}"/.ssh && \
    chmod 0700 /home/"${USER_NAME}"/.ssh && \
    ssh-keyscan bitbucket.org > /home/"${USER_NAME}"/.ssh/known_hosts && \
    echo "${SSH_KEY}" > /home/"${USER_NAME}"/.ssh/id_rsa && \
    echo "${SSH_KEY_PUB}" > /home/"${USER_NAME}"/.ssh/id_rsa.pub && \
    chmod 600 /home/"${USER_NAME}"/.ssh/id_rsa && \
    printf "Host bitbucket.org\nIdentityFile /home/${USER_NAME}/.ssh/id_rsa\n" > /home/"${USER_NAME}"/.ssh/config && \
    chmod 600 /home/"${USER_NAME}"/.ssh/config && \
    chown -R "${USER_ID}":"${USER_GID}" /home/"${USER_NAME}"/