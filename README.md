
Installation:
-------------------
docker setup
------------
1. sudo apt-get install \
    apt-transport-https \
    ca-certificates \
    curl \
    software-properties-common

2. curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -

3. sudo add-apt-repository \
   "deb [arch=amd64] https://download.docker.com/linux/ubuntu \
   $(lsb_release -cs) \
   stable"

4. sudo apt-get update && sudo apt-get install docker-ce
 to test run sudo docker run hello-world
5. sudo usermod -aG docker $(whoami)

Docekr-compose:
1. sudo curl -L https://github.com/docker/compose/releases/download/1.21.2/docker-compose-$(uname -s)-$(uname -m) -o /usr/local/bin/docker-compose

2. sudo chmod +x /usr/local/bin/docker-compose
3. docker-compose --version

OR:
1. sudo apt-get -y install python-pip
2. pip install docker-compose


1). PHP 7.3 Install:
```
#sudo apt-get install php7.2 php-pear php7.2-zip php7.2-curl php7.2-gd php7.2-mysql php7.2-mcrypt php7.2-xml php7.2-mbstring php7.2-pgsql php7.2-imagick php7.2-xmlrpc php7.2-sqlite3 php7.2-soap php7.2-tidy php7.2-recode php7.2-pspell php7.2-imap
sudo apt-get install php7.3 php7.3-fpm php-pear php-imagick php7.3-intl php7.3-zip php7.3-curl php7.3-gd php7.3-mysql php7.3-xml php7.3-mbstring php7.3-pgsql php7.3-xmlrpc php7.3-sqlite3 php7.3-soap php7.3-tidy php7.3-recode php7.3-pspell php7.3-imap
sudo update-alternatives --config php


```

2). Composer INSTALL:

```
composer install
```
3). Select ENVIRONMENT:
```
./init (select ENV)
```

MySQL 8.0.13:
-------------------
```
DB name: sales
DB user: sales
DB pass:
```

Migration (RBAC + LOG):
-------------------
```
./yii migrate --migrationPath=@yii/rbac/migrations/
./yii migrate --migrationPath=@yii/log/migrations/
./yii migrate
```

Create MySQL Dump:
```
mysqldump -v -u sale -p sale | gzip > `date +/var/www/backups/sql.%Y%m%d.gz`

gunzip < /path/to/outputfile.sql.gz | mysql -v -u USER -pPASSWORD DATABASE
```



Prod Hosts:
-------------------
 - [https://sales.travelinsides.com](https://sales.travelinsides.com) - Frontend
 - [https://sales.travelinsides.com/admin](https://sales.travelinsides.com/admin) - Backend
 - [https://sales.api.travelinsides.com](https://sales.api.travelinsides.com) - API
 - [https://sales.api.travelinsides.com/doc/index.html](https://sales.api.travelinsides.com/doc/index.html) - API Documentation

 Dev Hosts:
 -------------------
  - [http://sales.dev.travelinsides.com](http://sales.dev.travelinsides.com) - Frontend
  - [http://sales.dev.travelinsides.com/admin](http://sales.dev.travelinsides.com/admin) - Backend
  - [http://api-sales.dev.travelinsides.com](http://api-sales.dev.travelinsides.com) - API
  - [http://api-sales.dev.travelinsides.com/doc/index.html](http://api-sales.dev.travelinsides.com/doc/index.html) - API Documentation

 New Dev Hosts:
 -------------------
 - [https://sales-dev.travelinsides.com](https://sales-dev.travelinsides.com) - Frontend
 - [https://sales-dev.travelinsides.com/admin](https://sales-dev.travelinsides.com/admin) - Backend

 Generate API Documentation (apiDoc):
 ```
 sudo apidoc -i "./webapi/modules" -o "./webapi/web/doc" -f ".*\\.php$"
 ```

Install Supervisor
 -------------------
 ```
 sudo apt-get install supervisor
 sudo nano /etc/supervisor/supervisord.conf
 ```
 Update global config file (code):
 ```
 [include]
 files = /var/www/.../common/config/supervisor/*.conf
 ```
 
 Create supervisor config file (rename to socket-server.conf):
 ```
 /var/www/.../common/config/supervisor/socket-server.conf.txt
 ```
 
  Create supervisor config file (rename to queue-email-job.conf):
  ```
  /var/www/.../common/config/supervisor/queue-email-job.conf.txt
  ```

 
 
 Start supervisor service:
 ```
 
 sudo service supervisor start (OR sudo /etc/init.d/supervisor restart)
 #sudo apt-get install php-xmlrpc -y (optional)
 ``` 
  

Beanstalk:
-------------------
Driver for queue

(https://github.com/yiisoft/yii2-queue/blob/master/docs/guide-ru/driver-beanstalk.md)
```
        // example in yii components
        'queue_email_job' => [
            'class' => \yii\queue\beanstalk\Queue::class,
            'host' => 'localhost',
            'port' => 11300,
            'tube' => 'queue_email_job',
        ],
```

*Install on Ubuntu:*
```
apt-get install beanstalkd
```
*Install on Centos:*
```
yum install beanstalkd
```

*Run service:*
```
service beanstalkd start
```

----------


 Api Example:
 -------------------

POST - ```https://sales.api.travelinsides.com/v1/lead/create``` :

CURL Example:
```
curl -X POST \
  'http://sales.api.travelinsides.com/v1/lead/create?debug=1' \
  -H 'authorization: Basic YmFja29mZmljZTpiZl90ZXN0MjAxOA==' \
  -H 'cache-control: no-cache' \
  -H 'content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW' \
  -H 'postman-token: 4f7d0470-f7c1-93fc-2539-10144d213666' \
  -F 'lead[emails][0]=chalpet@gmail.com' \
  -F 'lead[emails][1]=chalpet2@gmail.com' \
  -F 'lead[phones][0]=+373-69-98-698' \
  -F 'lead[phones][1]=+373-69-98-698' \
  -F 'lead[flights][0][origin]=BOS' \
  -F 'lead[flights][0][destination]=LGW' \
  -F 'lead[flights][0][departure]=2018-09-19' \
  -F 'lead[flights][1][origin]=LGW' \
  -F 'lead[flights][1][destination]=BOS' \
  -F 'lead[flights][1][departure]=2018-09-22' \
  -F 'lead[source_id]=38' \
  -F 'lead[adults]=1' \
  -F 'lead[client_first_name]=Alexandr' \
  -F 'lead[client_last_name]=Freeman'
```

Documentation is at [docs/guide/README.md](docs/guide/README.md).

Test Code - Migration:
-------------------
common/config/test-local.php
```$php
'db' => [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=localhost;port=5432;dbname=test-sales',
    'username' => 'postgres',
    'password' => 'root',
    'charset' => 'utf8',
    'enableSchemaCache' => false,
],
```


```
./yii_test migrate --migrationPath=vendor/webvimark/module-user-management/migrations/
./yii_test migrate/up --migrationPath=@vendor/lajax/yii2-translate-manager/migrations
./yii_test migrate --migrationPath=@yii/log/migrations/
```

[https://codeception.com/docs/02-GettingStarted](https://codeception.com/docs/02-GettingStarted#Generators)

* generate acceptance ```../vendor/bin/codecept generate:cest acceptance Index```
* build test ```vendor/bin/codecept build```
* run custom test  ```../vendor/bin/codecept run tests/acceptance/IndexCest.php```
* run test ```vendor/bin/codecept run```


PhantomJS
-------------------
* Download & install - [http://phantomjs.org/download.html](http://phantomjs.org/download.html)
* Run  ```phantomjs --webdriver=4444```


WebSocket Server (https://github.com/walkor/Workerman):
```
sudo php console/socket-server.php start
sudo php console/socket-server.php stop
sudo php console/socket-server.php restart
sudo php console/socket-server.php reload

```


Check process by PORT
```
sudo netstat -tulpn| grep :8080
```

CRONs
-------------------
```
*/15  *  *  *  *     run-this-one php /var/www/sale/yii monitor-flow/follow-up
*/15  *  *  *  *     run-this-one php /var/www/sale/yii monitor-flow/on-wake
*/30  *  *  *  *     run-this-one php /var/www/sale/yii monitor-flow/watch-dog-decline-quote
*/3   *  *  *  *     run-this-one php /var/www/sale/yii lead/update-ip-info
10   0  *  *  *     run-this-one php /var/www/sale/yii lead/update-by-tasks
30   0  *  *  *     run-this-one php /var/www/sale/yii db/update-airline-cabin-classes
40   0  1  *  *     php /var/www/sale/yii kpi/calculate-salary
```

DIRECTORY STRUCTURE
-------------------

```
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
    tests/               contains tests for common classes
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for backend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for frontend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
webapi
    config/              contains webapi configurations
    controllers/         contains Web controller classes
    models/              contains webapi-specific model classes
    runtime/             contains files generated during runtime
    web/                 contains the entry script and Web resources
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```