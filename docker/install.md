
Для запуска данного шаблона необходимы [docker](https://docs.docker.com/engine/getstarted/step_one/) и [docker-compose](https://docs.docker.com/compose/install/)
> Обратите вниимание на особенности установки - apt не позволяет добыть нужные версии

Ниже приведу последовательность действий для запуска на Ubuntu

1.Установить docker и docker-compose по ссылкам выше.

1.1. Создать файл локальных настроек `.env`  
`.env_example` - пример локальных настроек `docker-compose`.
Должен быть скопирован в `.env` для запуска в режиме разработки `docker-compose up -d`.
Файл `.env` должен присутствовать в папке из которой выполняется команда `docker-compose`.
```sh
cp .env.example .env
```
Проверьте `.env` файл на наличие обязательных параметров для Docker:
```yaml
# DOCKER-COMPOSE CONFIG
# ---------------------------------------------------------------------------------
APP_PTH_CONTAINER=/app
APP_PATH_HOST=./

PHP_VERSION=7.4-fpm
COMPOSER_VERSION=2.1.8
NGINX_VERSION=1.18.0-alpine

MYSQL_VERSION=8.0.26
MYSQL_HOST=mysql
MYSQL_ROOT_PASSWORD=4fe9fcc73ed12a7fcec46
MYSQL_DATABASE=crm
MYSQL_USER=crmuser
MYSQL_PASSWORD=Password123

POSTGRES_VERSION=13-alpine
POSTGRESL_HOST=pgsql
POSTGRES_DB=crm
POSTGRES_USER=crmuser
POSTGRES_PASSWORD=Password123
# ---------------------------------------------------------------------------------
app.path=APP_PTH_CONTAINER
app.console.logfile.path=${APP_PTH_CONTAINER}/console/runtime"
```


2.Создать папку проекта

```sh
$ git clone https://bitbucket.org/techork/sale.git /var/www/crm.office.test/www/
```

3.Загрузить и запустить сервис `php`
> Если Вы хотите запустить на одной машине несколько копий такой сборки - обратите внимани на то, чтобы папки (и соответственно префикс композиции, в примере "dockerrun_") имели разное название. Также переменные окружения для mysql необходимо дифференцировать по проектам. Несоблюдение данного правила будет приводить к ошибкам подключения к базе.


> Одиночные команды можно выполнить и без этого
`docker-compose run --rm php composer update`

4.Загрузить зависимости `composer` в контейнере. Обнление потребует github token (см. [установку yii2](https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/start-installation.md) ), его вы можете найти на своей странице в разделе `https://github.com/settings/tokens`

Кеш композера можно вынести из контейнера, для поддержания его в чистоте и ускорения работы новых контейнеров сервиса `php`
```sh
- ~/.composer/cache:/root/.composer/cache
```

```sh
composer update -vv
```

5.Инициализировать шаблон скриптом, аналогично исходному [шаблону advanced](https://github.com/yiisoft/yii2-app-advanced/blob/master/docs/guide/README.md)

Выберете покружение `development [0]` в скрипте инициализации
```sh
./init
``` 
что создаст настройки и скрипт `yii` для следующего шага. Настройки базы уже установлены для окружения,
их согласно вашим нуждам можно изменить(`php-data/common/config/main.php` - требуется root).
> Внимание! Возникла ошибка доступа? При изменении настроек базы после её первого запуска не забываем останавливать композицию `docker-compose down` и чистить файлы базы `sudo rm -rf ../mysql-data/*`; Возникла ошибка `SQLSTATE[HY000] [2002] Connection refused` - база не успела поднятся.

5.1.Выполнить миграции внутри контейнера

```sh
/usr/local/bin/docker-compose exec php ./yii migrate/up
```

> Самое время создать дамп базы (например, такой метод использовался при создании используемого в тестах). При запущенном контейнере `mysql`
используем согласно [документации в описании образа](https://hub.docker.com/_/mysql/)
```sh
docker exec mysql sh -c 'exec mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" crm' > docker/mysql/dump/crm-nodata.sql
```
При восстановлении необходимо добавить ключ `-i` для перенаправления ввода.
```sh
docker exec -i mysql sh -c 'exec mysql -uroot -p"$MYSQL_ROOT_PASSWORD" crm' < docker/mysql/dump/crm-nodata.sql
```

5.2.При выполнении последней мигации вы проведёте инициализацию rbac [см. общая инструкция установки шаблона](./guide/start-installation.md). **Первый пользователь получит права администратора**.

> В случае ошибки на этапе создания первого пользователя права не будут выданы. Верните базу в первоначальный вид и попробуйте снова.

6.Выйти из контейнера (`exit`, ctrl+c) и запустить комплекс сервисов
```sh
$ docker-compose up -d
Starting adminer    ... done
Starting web        ... done
Starting mysql      ... done
Starting centrifugo ... done
Starting redis      ... done
Starting pgsql      ... done
Starting beanstalkd ... done
Starting composer   ... done
Starting nginx      ... done
```

> Для работы с доменом необходимо прописать записи в `/etc/hosts`.

```shell
cat /etc/hosts
127.0.0.1	crm.office.test
127.0.0.1	api.crm.office.test
```
Сервисы доступны по адресам:
* [http://crm.office.test:8082](http://crm.office.test:8082/) - Frontend
* [http://localhost:8084/](http://localhost:8084/) - Adminer
* [http://localhost:8001/](http://localhost:8001/) - Centrifugo

### Создание DUMP для MySQL и PostgreSQL
> Для создания дампа базы данных `MySQL` возможно использование команды:

только структура таблиц:
```shell
mysqldump -v -e -u root -h localhost --no-data --no-create-info -p [DATABASE] > /var/www/sales.office.test/crm-nodata.sql
```
только данные выбранных таблиц:
```shell
mysqldump -v -e -u root -h localhost --no-create-info -p [DATABASE] setting_category setting migration api_user api_user_allowance projects project_locale project_relation project_weight sources language language_source language_translate employees employee_contact_info cron_scheduler auth_item auth_item_child auth_assignment auth_rule abac_policy airlines airports airport_lang app_project_key case_category shift shift_schedule_rule status_weight user_group user_group_assign user_group_set user_params user_product_type user_profile user_project_params project_email_templates project_employee_access > /var/www/crm.office.test/crm2-data.sql
```

> Для создания дампа базы данных `PosgreSQL` возможно использование команды:
> ```shell
> pg_dump -h localhost --username=postgres -Fp --password --verbose --no-owner -T 'api_log_*' -T 'client_chat_message_*' -T 'client_chat_request_*' DATABASE > /var/www/crm.office.test/crm-nodata-pgsql.sql
> ```

### Дополнительные полезные Docker команды
Получить список контейнеров:
```shell
$ docker-compose ps 
```
команда отобразит следущее:
```shell
   Name                 Command               State                                      Ports                                   
---------------------------------------------------------------------------------------------------------------------------------
adminer      entrypoint.sh docker-php-e ...   Up       0.0.0.0:8084->8080/tcp,:::8084->8080/tcp                                  
beanstalkd   /usr/bin/beanstalkd              Up       11300/tcp                                                                 
centrifugo   centrifugo -c config.json        Up       0.0.0.0:8001->8000/tcp,:::8001->8000/tcp                                  
composer     /docker-entrypoint.sh comp ...   Exit 0                                                                             
mysql        docker-entrypoint.sh mysqld      Up       0.0.0.0:3307->3306/tcp,:::3307->3306/tcp, 33060/tcp                       
nginx        /docker-entrypoint.sh ngin ...   Up       0.0.0.0:8082->80/tcp,:::8082->80/tcp, 0.0.0.0:8081->81/tcp,:::8081->81/tcp
pgsql        docker-entrypoint.sh postgres    Up       0.0.0.0:6432->5432/tcp,:::6432->5432/tcp                                  
redis        docker-entrypoint.sh redis ...   Up       6379/tcp                                                                  
web          docker-php-entrypoint php-fpm    Up       9000/tcp, 9501/tcp  
```

Вход в контейнер `web`:
```shell
$ docker exec -it web bash
```

Удаление контейнера `mysql`:
```shell
$ docker-compose rm mysql
```

Сборка контейнеров:
```shell
$ docker-compose build
```