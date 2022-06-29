#!/bin/bash

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

initCurrentUserVars

initEnv () {
  if [ ! -e "$dockerFolder/.env" ]; then
    cp "$dockerFolder/.env.example" "$dockerFolder/.env"
    printf "\n\n# User info\nUSER_NAME=%s\nUSER_ID=%s\nUSER_GID=%s" "$CURRENT_USER" "$CURRENT_UID" "$CURRENT_GROUP_ID" >> "$dockerFolder/.env"
    printf "\n\n# Ssh info\nSSH_KEY=%s\nSSH_KEY_PUB=%s\n" "/home/$CURRENT_USER/.ssh/id_rsa" "/home/$CURRENT_USER/.ssh/id_rsa.pub" >> "$dockerFolder/.env"
    printf "docker/ENV file is created\n"
  fi
  if [ ! -e ".env" ]; then
    cp ".env.example" ".env"
    printf "ENV file is created\n"
  fi
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
  printf "Not found .docker/.env Please run init-env command\n"
  exit;
elif [ ! -e ".env" ]; then
  printf "Not found .env Please run init-env command\n"
  exit;
fi

getEnvVar () {
  echo $(cat "$dockerFolder/.env" | grep "$1=" | cut -d "=" -f 2 );
}

REGISTRY=$(getEnvVar "REGISTRY")

VIRTUAL_HOST=$(getEnvVar "VIRTUAL_HOST")
VIRTUAL_HOST_PORT=$(getEnvVar "VIRTUAL_HOST_PORT")

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

createDefaultCentrifugoConfig () {
  cp -l "$dockerFolder/centrifugo/config.json.example" "$dockerFolder/centrifugo/config.json"
}

removeDefaultCentrifugoConfig () {
  sudo rm "$dockerFolder/centrifugo/config.json"
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
  removeDefaultCentrifugoConfig
  ls -d -1 "$dockerFolder/nginx/certs/mkcert/"*.* | xargs rm
  printf "\n"
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

  printf "Open URL: https://$VIRTUAL_HOST:$VIRTUAL_HOST_PORT \n"
  printf "Server is started\n"
}

down () {
  docker-compose -f "$dockerComposeFile" down --remove-orphans
}

restart () {
  down
  start
}

createKivorkNetwork () {
  if [ -z $(docker network ls -f name=kivork-proxy -q) ]; then
    printf "\nStart - Create kivork-proxy network \n\n"
    docker network create kivork-proxy
    printf "\nDone - Create kivork-proxy network \n\n"
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
  createDefaultCentrifugoConfig
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
  printf "If you have already run the command before, running the command again \n";
  printf "will recreate the directories and delete the existing files and database\n";
  read -p "Continue (y/n)? " answer
  case ${answer:0:1} in
      y|Y )
            printf "Start - Application install\n"
            applicationUninstall
            createTemporallyDirectories
            build
            initConfig
            initChown
            initMysql
            initPsql
            composerInstall
            npmInstall
            printf "Done - Application install\n"
      ;;
  esac
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

if [ "$whatDo" == "application-install" ]; then
  applicationInstall

elif [ "$whatDo" == "application-uninstall" ]; then
  applicationUninstall

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
elif [ "$whatDo" == "app-console" ]; then
  docker-compose -f "$dockerComposeFile" run --rm console bash

elif [ "$whatDo" == "apidoc-gen" ]; then
  docker-compose -f "$dockerComposeFile" run --rm --no-deps console composer apidoc-gen

elif [ "$whatDo" == "init-config" ]; then
  initConfig

elif [ "$whatDo" == "migrate" ]; then
  runMigrate

elif [ "$whatDo" == "init-local-bo" ]; then
  useLocalBo=$(cat $dockerFolder/.env | grep 'USE_LOCAL_BO' | cut -d "=" -f 2)
  useLocalBoUrl=$(cat $dockerFolder/.env | grep 'NGINX_CONTAINER_LOCAL_BO' | cut -d "=" -f 2)

  boUrlReal=$(cat .env | grep 'COMMON_CONFIG_PARAMS_BACKOFFICE_SERVERURL=' | cut -d "=" -f 2)
  boUrlReal2=$(cat .env | grep 'COMMON_CONFIG_PARAMS_BACKOFFICE_SERVERURLV2=' | cut -d "=" -f 2)
  boUrlReal3=$(cat .env | grep 'COMMON_CONFIG_PARAMS_BACKOFFICE_SERVERURLV3=' | cut -d "=" -f 2)

  boUrlLocal="http://$useLocalBoUrl/api/sync"
  boUrlLocal2="http://$useLocalBoUrl/api/v2"
  boUrlLocal3="http://$useLocalBoUrl/api/v3"

  check=$(echo "$boUrlReal" | grep "$boUrlLocal")

  if [ "$useLocalBo" == "true" ]; then
    if [ "$check" == "" ]; then
        printf -v boUrl "%s\t##%s" "$boUrlLocal" "$boUrlReal"
        printf -v boUrl2 "%s\t##%s" "$boUrlLocal2" "$boUrlReal2"
        printf -v boUrl3 "%s\t##%s" "$boUrlLocal3" "$boUrlReal3"

        sed -i "s~$boUrlReal~$boUrl~" .env
        sed -i "s~$boUrlReal2~$boUrl2~" .env
        sed -i "s~$boUrlReal3~$boUrl3~" .env

        printf "Init local env\n"
    fi
  else
      if [ "$check" != "" ]; then
        sed -i "s~$boUrlLocal\t##~~" .env
        sed -i "s~$boUrlLocal2\t##~$boUrl2~" .env
        sed -i "s~$boUrlLocal3\t##~$boUrl3~" .env

        printf "Disabled local env\n"
    fi
  fi

else
  printf "Unknown command\n"
fi