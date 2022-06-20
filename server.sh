#!/bin/bash

initEnv () {
  if [ ! -e "$dockerFolder/.env" ]; then
    cp "$dockerFolder/.env.example" "$dockerFolder/.env"
    printf "ENV file is created\n"
  fi
}

getEnvVar () {
  echo $(cat "$dockerFolder/.env" | grep "$1=" | cut -d "=" -f 2 );
}

dirName=$(dirname "$0")
currentDir=$(cd "$dirName" && pwd)
dockerFolder="$currentDir/.docker"
dockerComposeFile="$dockerFolder/docker-compose.yaml"
whatDo="$1"

if [ "$whatDo" == "init-env" ]; then
  initEnv
  exit;
elif [ ! -e "$dockerFolder/.env" ]; then
  printf "Not found .env Please run init-env command\n"
  exit;
fi

REGISTRY=$(getEnvVar "REGISTRY")

VIRTUAL_HOST=$(getEnvVar "VIRTUAL_HOST")

NGINX_IMAGE_TAG=$(getEnvVar "NGINX_IMAGE_TAG")
NGINX_VERSION=$(getEnvVar "NGINX_VERSION")

PHP_FPM_IMAGE_TAG=$(getEnvVar "PHP_FPM_IMAGE_TAG")
PHP_FPM_VERSION=$(getEnvVar "PHP_FPM_VERSION")

PHP_CLI_IMAGE_TAG=$(getEnvVar "PHP_CLI_IMAGE_TAG")
PHP_CLI_VERSION=$(getEnvVar "PHP_CLI_VERSION")

MYSQL_IMAGE=$(getEnvVar "MYSQL_IMAGE")
MYSQL_VERSION=$(getEnvVar "MYSQL_VERSION")
MYSQL_ROOT_PASSWORD=$(getEnvVar "MYSQL_ROOT_PASSWORD")
MYSQL_USER=$(getEnvVar "MYSQL_USER")
MYSQL_PASSWORD=$(getEnvVar "MYSQL_PASSWORD")
MYSQL_DATABASE=$(getEnvVar "MYSQL_DATABASE")

POSTGRES_VERSION=$(getEnvVar "POSTGRES_VERSION")
POSTGRES_USER=$(getEnvVar "POSTGRES_USER")
POSTGRES_PASSWORD=$(getEnvVar "POSTGRES_PASSWORD")
POSTGRES_DB=$(getEnvVar "POSTGRES_DB")

KIVORK_PROXY_REVERSE_DIR=$(getEnvVar "KIVORK_PROXY_REVERSE_DIR")

tmpDirs=(
  "$dockerFolder/api-nginx/logs"
  "$dockerFolder/centrifugo/logs"
  "$dockerFolder/centrifugo-nginx/logs"
  "$dockerFolder/console/.composer"
  "$dockerFolder/console/.composer/cache"
  "$dockerFolder/frontend-nginx/logs"
  "$dockerFolder/mysql/data"
  "$dockerFolder/psql/data"
  "$dockerFolder/ws-nginx/logs"
)

createTemporallyDirectories () {
  printf "\nCreate temporally directories"
  for str in ${tmpDirs[@]}; do
    if [ ! -e $str ]; then
      printf "\nCreate $str directory"
      mkdir -p $str
    else
      printf "\nDirectory $str already exist"
    fi
  done
  printf "\n"
}

removeTemporallyDirectories () {
  printf "\nRemove temporally directories"
  for str in ${tmpDirs[@]}; do
    if [ ! -e $str ]; then
      printf "\nDirectory $str already removed"
    else
      printf "\nRemove $str directory"
      sudo rm -r $str
    fi
  done
  ls -d -1 "$dockerFolder/nginx/certs/mkcert/"*.* | xargs rm
  printf "\n"
}

initCurrentUserVars() {
    unameOut="$(uname -s)"
    case "${unameOut}" in
        Linux*)     machine=Linux;;
        Darwin*)    machine=Mac;;
        CYGWIN*)    machine=Cygwin;;
        MINGW*)     machine=MinGw;;
        *)          machine="UNKNOWN:${unameOut}"
    esac

    case $machine in
      Mac)
        CURRENT_UID="$(id -u)"
        CURRENT_USER="$(id -un)"
        CURRENT_GROUP_ID="$(id -g)"
        CURRENT_GROUP="$(id -gn)"
#        echo -n "user_id:"  ${CURRENT_UID} " user_name:"  ${CURRENT_USER} " group_id:"  ${CURRENT_GROUP_ID} " "
        ;;
      Linux)
        CURRENT_UID=${UID}
        CURRENT_USER=${USER}
        CURRENT_GROUP_ID=${UID}
        CURRENT_GROUP=${USER}
        ;;
      *)
        printf "Required variables were not initialized\n"
        exit
        ;;
    esac
}

kivorkReverseProxyRestart () {
  kivorkReverseProxyDown
  kivorkReverseProxyUp
}

kivorkReverseProxyUp () {
  docker-compose -f "$KIVORK_PROXY_REVERSE_DIR/docker-compose.yaml" up -d --remove-orphans
}

kivorkReverseProxyDown () {
  docker-compose -f "$KIVORK_PROXY_REVERSE_DIR/docker-compose.yaml" down --remove-orphans
}

logoutRoot() {
    if [ "$EUID" -eq 0 ]; then
      printf "\nRoot user has been logout\n"
      exit
    fi
}

stop () {
  for containerId in $(docker ps -f name=^crm -q) ; do
    docker stop "$containerId"
  done
  printf "Server is stopped\n"
}

start () {
  dirToCert="$dockerFolder/nginx/certs"
  if [ ! -e "$dirToCert/mkcert/devcert.crt" ]; then
    cp "$dirToCert/devcert.crt" "$dirToCert/mkcert/devcert.crt"
    cp "$dirToCert/devcert.key" "$dirToCert/mkcert/devcert.key"
  fi

  docker-compose -f "$dockerComposeFile" up -d --remove-orphans
  runMigrate

  kivorkReverseProxyRestart

  printf "Open URL: https://$VIRTUAL_HOST \n"
  printf "Server is started\n"
}

up () {
  docker-compose -f "$dockerComposeFile" up -d --remove-orphans
}

down () {
  docker-compose -f "$dockerComposeFile" down --remove-orphans
}

restart () {
  down
  start
}

createKivorkNetwork () {
  if [ -z $(docker network ls -f name=kivork-network -q) ]; then
    printf "\nStart - Create kivork-network network \n\n"
    docker network create kivork-network
    printf "\nDone - Create kivork-network network \n\n"
  fi
}

build () {
  down
  createKivorkNetwork

  printf "\nStart - Build \n\n"
  docker build --build-arg NGINX_VERSION=$NGINX_VERSION --build-arg USER_NAME=$CURRENT_USER --build-arg USER_ID=$CURRENT_UID --build-arg USER_GID=$CURRENT_GROUP_ID --file="$dockerFolder/nginx/Dockerfile" --tag="${REGISTRY}"crm-nginx:$NGINX_IMAGE_TAG $dockerFolder/nginx
	docker build --build-arg PHP_FPM_VERSION=$PHP_FPM_VERSION --build-arg USER_NAME=$CURRENT_USER --build-arg USER_ID=$CURRENT_UID --build-arg USER_GID=$CURRENT_GROUP_ID --file="$dockerFolder/php-fpm/Dockerfile" --tag="${REGISTRY}"crm-php-fpm:$PHP_FPM_IMAGE_TAG $dockerFolder/php-fpm
	docker build --build-arg PHP_CLI_VERSION=$PHP_CLI_VERSION --file="$dockerFolder/php-cli/Dockerfile" --tag="${REGISTRY}"crm-php-cli:$PHP_CLI_IMAGE_TAG $dockerFolder/php-cli
  docker-compose -f "$dockerComposeFile" build
  printf "\nDone - Build \n\n"
}

destroy () {
  stop

  for containerId in $(docker ps -f name=^crm -a -q) ; do
    docker rm "$containerId"
  done
}

applicationUninstall () {
  destroy

#  if [ -e "$currentDir/.docker/mysql/data" ]; then
#    sudo rm -r -d "$currentDir/.docker/mysql/data"
#    logoutRoot
#  fi

  removeTemporallyDirectories
  logoutRoot

  printf "Server is destroyed\n"
}

initChown () {
    sudo chown -R "$CURRENT_USER":"$CURRENT_GROUP" "$currentDir/frontend/runtime/"
    sudo chown -R "$CURRENT_USER":"$CURRENT_GROUP" "$currentDir/frontend/web/assets/"
    sudo chown -R "$CURRENT_USER":"$CURRENT_GROUP" "$currentDir/console/runtime/"
    sudo chown -R "$CURRENT_USER":"$CURRENT_GROUP" "$currentDir/webapi/runtime/"
    sudo chown -R "$CURRENT_USER":"$CURRENT_GROUP" "$currentDir/yii"
    sudo chown -R "$CURRENT_USER":"$CURRENT_GROUP" "$currentDir/yii_test"
}

composerInstall () {
  printf "Start - Composer install\n"
  docker-compose -f "$dockerComposeFile" run --no-deps --rm console composer install
  printf "\nDone - Composer install\n\n"
}

npmInstall () {
  printf "Start - Npm install\n"
  docker-compose -f "$dockerComposeFile" run --no-deps --rm console npm install
  printf "\nDone - Npm install\n\n"
}

initConfig () {
  printf "Start - Init config \n"
	docker run --rm -v $PWD:/var/www/app \
    --workdir /var/www/app \
    --user $CURRENT_UID:$CURRENT_GROUP_ID \
    crm-php-cli:$PHP_CLI_IMAGE_TAG \
    php init --env=Local --overwrite=All \
    && rm "common/config/supervisor/centrifugo.conf" \
    && rm "common/config/supervisor/socket-server.conf" \
    && cp ".docker/pre-commit.sh" "docker-pre-commit.sh" \
    && chmod +x "docker-pre-commit.sh"
  printf "\nDone - Init config \n\n"
}

waitMysqlIsReady () {
  result=$(docker logs crm-mysql 2>&1 | grep "port: 3306  MySQL Community Server" )
  if [ -z "$result" ]; then
    sleep 3
    printf "."
    waitMysqlIsReady
  fi
}

initMysql () {
  printf "Init MySql\nRun MySql container\n"
  docker run -v "$dockerFolder/mysql/dump":/dump -v "$dockerFolder/mysql/bash":/bash -v "$dockerFolder/mysql/data":/var/lib/mysql --name crm-mysql -e MYSQL_ROOT_PASSWORD=$MYSQL_ROOT_PASSWORD -e MYSQL_USER=$MYSQL_USER -e MYSQL_PASSWORD=$MYSQL_PASSWORD -e MYSQL_DATABASE=$MYSQL_DATABASE -d --rm $MYSQL_IMAGE:$MYSQL_VERSION
  printf "Wait MySql is ready"
  waitMysqlIsReady
  printf "\n"
  printf "Start - Load Mysql dump\n"
  docker exec -it crm-mysql bash -c "exec /bash/load_dump.sh"
  printf "Done - Load Mysql dump\n\n"
  printf "Mysql container stop\n"
  docker container stop crm-mysql
}

waitPsqlIsReady () {
  result=$(docker logs crm-psql 2>&1 | grep "port 5432" )
  if [ -z "$result" ]; then
    sleep 3
    printf "."
    waitPsqlIsReady
  fi
}

initPsql () {
  printf "Init Psql\nRun Psql container\n"
  docker run -v "$dockerFolder/psql/dump":/dump -v "$dockerFolder/psql/bash":/bash -v "$dockerFolder/psql/data":/var/lib/postgresql/data --name crm-psql -e POSTGRES_USER=$POSTGRES_USER -e POSTGRES_PASSWORD=$POSTGRES_PASSWORD -e POSTGRES_DB=$POSTGRES_DB -d --rm postgres:$POSTGRES_VERSION
  printf "Wait Psql is ready"
  waitPsqlIsReady
  printf "\n"
  printf "Start - Load Psql dump\n"
  docker exec -it crm-psql bash -c "exec /bash/load_dump.sh"
  printf "Done - Load Psql dump\n\n"
  printf "Psql container stop\n"
  docker container stop crm-psql
}

runMigrate () {
  printf "Start - Migrate \n"
  docker-compose -f "$dockerComposeFile" run --rm console ./yii migrate
  printf "\nDone - Migrate\n\n"
}

applicationInstall () {
  printf "Start - Application install\n"
  applicationUninstall
  createTemporallyDirectories
  build
  initChown
  initConfig
#  initMysql
#  initPsql
  composerInstall
  npmInstall
  printf "Done - Application install\n"
}

dockerInstall () {
  printf "Uninstall old versions\n"
  sudo apt-get remove -y docker docker-engine docker.io containerd runc

  printf "Install dependent packages\n"
  dependentPackages=("apt-transport-https" "ca-certificates" "curl" "gnupg" "lsb-release")
  for pkg in "${dependentPackages[@]}"; do
    sudo apt-get install -y "$pkg"
  done

  printf "Set up the repository\n"
  curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
  echo "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

  printf "\nInstall Docker Engine\n"
  sudo apt-get update
  sudo apt-get install -y docker-ce docker-ce-cli containerd.io

  printf "\nDocker as a non-root user\n"
  sudo groupadd docker && sudo usermod -aG docker "$CURRENT_USER" && newgrp docker

  if [ -e /home/"$CURRENT_USER"/.docker ]; then
    sudo chown "$CURRENT_USER":"$CURRENT_USER" /home/"$CURRENT_USER"/.docker -R
    sudo chmod g+rwx "$HOME/.docker" -R
  fi

  printf "\nConfigure Docker to start on boot\n"
  sudo systemctl enable docker.service
  sudo systemctl enable containerd.service

  printf "\nInstall Docker compose\n"
  sudo sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose

  if [ ! -e /usr/bin/docker-compose ]; then
    printf "\nCreate a symbolic link Docker compose to /usr/bin\n"
    sudo ln -s /usr/local/bin/docker-compose /usr/bin/docker-compose
  fi

  sudo chmod +x /usr/local/bin/docker-compose
}

logoutRoot
initCurrentUserVars

if [ "$whatDo" == "application-install" ]; then
    applicationInstall

elif [ "$whatDo" == "application-uninstall" ]; then
  applicationUninstall

elif [ "$whatDo" == "up" ]; then
  up

elif [ "$whatDo" == "down" ]; then
  down

elif [ "$whatDo" == "start" ]; then
  start

elif [ "$whatDo" == "stop" ]; then
  stop

elif [ "$whatDo" == "stop-all" ]; then
  docker stop $(docker ps -q)

elif [ "$whatDo" == "restart" ]; then
 restart

elif [ "$whatDo" == "build" ]; then
  build

elif [ "$whatDo" == "composer" ]; then
    if [ "$2" == "" ]; then
        params=" help"
    else
        params="$2"
    fi

    printf "Start - Composer \n"
    docker-compose -f "$dockerComposeFile" run --no-deps --rm console composer $params
    printf "Done - Composer\n"

elif [ "$whatDo" == "npm" ]; then
    if [ "$2" == "" ]; then
        params=" help"
    else
        params="$2"
    fi

    printf "Start - Npm \n"
    docker-compose -f "$dockerComposeFile" run --no-deps --rm console npm $params
    printf "Done - Npm\n"

elif [ "$whatDo" == "docker-install" ]; then
  dockerInstall

elif [ "$whatDo" == 'yii' ]; then
  params="$@"
  docker-compose -f "$dockerComposeFile" run --rm console /bin/sh -c "./$params"

elif [ "$whatDo" == 'cert-install' ] || [ "$whatDo" == 'cert-update' ]; then
  mkcertVersion="$(mkcert --version 2> /dev/null)"

  if [ "$mkcertVersion" == '' ] || [ "$whatDo" == 'cert-update' ]; then
    printf "Installing mkcert\n"

    linkRepositoryMkcert="https://api.github.com/repos/FiloSottile/mkcert/releases/latest"
    urlMkcert=$(curl -s "$linkRepositoryMkcert" | grep browser_download_url  | grep linux-amd64 | cut -d '"' -f 4)
    if [ "$urlMkcert" != '' ]; then
      sudo apt install libnss3-tools wget
      sudo wget "$urlMkcert" -O /usr/local/bin/mkcert
      sudo chmod a+x /usr/local/bin/mkcert
      mkcertVersion="$(mkcert --version)"

      if [ "$mkcertVersion" != '' ]; then
        printf "Installation was done. Mkcert version: %s\n" "$mkcertVersion"

        printf "Creating a new local CA\n"
        mkcert -install

        stop

        printf "Creating a new certificate valid for the %s\n" $VIRTUAL_HOST

        dirToCert="$dockerFolder/nginx/certs/mkcert"
        if [ -e "$dirToCert/devcert.key" ]; then
          rm "$dirToCert/devcert.key" "$dirToCert/devcert.crt"
        fi
        mkcert -key-file "$dirToCert/devcert.key" -cert-file "$dirToCert/devcert.crt" "$VIRTUAL_HOST" "*.$VIRTUAL_HOST"
      else
        printf "Installation error\n"
      fi
    else
      printf "Repository link '%s' is bad;" "$linkRepositoryMkcert"
    fi
  else
    printf "Mkcert has been already installed\n"
  fi
elif [ "$whatDo" == "test" ]; then
  printf "Test"
else
  printf "Unknown command\n"
fi