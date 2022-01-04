Minimal Installation setup for local stage development server:
(full toolkit setup documentation located at https://traveldev.atlassian.net/wiki/spaces/SLS/pages/1584791640/ )

-------------------
1). Install PHP 7.4 and redis-server :
```
sudo apt-get install php7.4 php7.4-fpm php-pear php-imagick php7.4-intl php7.4-zip php7.4-curl php7.4-gd php7.4-mysql php7.4-xml php7.4-mbstring php7.4-pgsql php7.4-xmlrpc php7.4-sqlite3 php7.4-soap php7.4-tidy php7.4-pspell php7.4-imap php7.4-bcmath php-redis php-ssh2
```

1.1) INSTALL swoole 4.4.16 & ext-async v4.4.16 for swoole_redis
```
cd ~
wget https://github.com/redis/hiredis/archive/v0.14.1.tar.gz && tar zxvf v0.14.1.tar.gz
cd hiredis-0.14.1/
make -j & sudo make install
sudo ldconfig
cd ..
 
git clone https://github.com/swoole/swoole-src
cd swoole-src/
git checkout tags/v4.4.16
phpize && ./configure
make -j 4 && sudo make install
 
git clone https://github.com/swoole/ext-async
cd ext-async/
git checkout tags/v4.4.16
phpize && ./configure
make -j 4 && sudo make install
``` 

Add this in “Dynamic Extensions” section if php-cli config file located at  /ect/php/7.4/cli/php.ini

```
extension=swoole
extension=swoole_async
```

2). Composer INSTALL:
```
composer install
```
3). Select ENVIRONMENT:
```
./init (select ENV)
```

Nginx configuration
-------------------
1. Copy example config file ./nginx.conf from project folder to your nginx configs directory usually located at /etc/nginx/sites-available/
2. (Optional) You can edit our test hosts from sales.zeit.test and api.sales.zeit.test to anything else (if you want)
3. Generate self-signed SSL certificate (for example you can use certutil or openssl) and edit in nginx config their file names for 2 hosts (add them to /etc/hosts too):
   sales.zeit.test
   api.sales.zeit.test

4. Also edit this parameter in nginx config:

```
#for API endpoint
location ~ \.php$ {
    ...
    fastcgi_read_timeout 90s;
}
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
mysqldump -Q -c -e -v -u USER -p DATABASE | gzip > /var/www/backups/sql.gz
gunzip < /var/www/sale/sql.gz | mysql -v -u USER -pPASSWORD DATABASE
gunzip < /var/www/sale/sql.gz | time mysql -u USER -pPASSWORD DATABASE --force
mysqlshow -u USER -pPASSWORD DATABASE
```
Create Pg Dump:
```
pg_dump -h localhost --username=postgres -Fc sales3 > /home/user/db.dump
```

edit /etc/mysql/conf.d/mysql.cnf
``` 
[mysqld]
sql_mode=only_full_group_by
```
sudo service mysql restart

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
sudo apt install beanstalkd
```
*Install on Centos:*
```
yum install beanstalkd
```

*Run service:*
```
service beanstalkd start
```

Cetrifugo Server
-------------------
*Install on Ubuntu:*

https://centrifugal.github.io/centrifugo/server/install/

```
# in .. config/supervisor rename file centrifugo.conf.txt
sudo service supervisor restart
```
END of installation. Now frontend and api must respond. 


DOCUMENTATION
----------

Prod Kiv Host:
-------------------
- [sales.travelinsides.com](https://sales.travelinsides.com) - Frontend
- [sales.api.travelinsides.com](https://sales.api.travelinsides.com) - API
- [sales.api.travelinsides.com/doc/index.html](https://sales.api.travelinsides.com/doc/index.html) - API Documentation
- [sales.api.travelinsides.com/phpdoc/index.html](https://sales.api.travelinsides.com/phpdoc/index.html) - PHP Documentation


Prod GTT Host:
-------------------
- [crm.gttglobal.com](https://crm.gttglobal.com) - Frontend
- [crm-api.gttglobal.com](https://crm-api.gttglobal.com) - API
- [crm-api.gttglobal.com/doc/index.html](https://crm-api.gttglobal.com/doc/index.html) - API Documentation

Stage Host:
-------------------
- [stage-sales.travel-dev.com](https://stage-sales.travel-dev.com) - Frontend
- [stage-sales-api.travel-dev.com](https://stage-sales-api.travel-dev.com) - API
- [stage-sales-api.travel-dev.com/doc/index.html](https://stage-sales-api.travel-dev.com/doc/index.html) - API Documentation
- [stage-sales-api.travel-dev.com/phpdoc/index.html](https://stage-sales-api.travel-dev.com/phpdoc/index.html) - PHP Documentation

Dev Host:
-------------------
- [sales.dev.travelinsides.com](https://sales.dev.travelinsides.com) - Frontend
- [api-sales.dev.travelinsides.com](https://api-sales.dev.travelinsides.com) - API
- [api-sales.dev.travelinsides.com/doc/index.html](https://api-sales.dev.travelinsides.com/doc/index.html) - API Documentation
- [api-sales.dev.travelinsides.com/phpdoc/index.html](https://api-sales.dev.travelinsides.com/phpdoc/index.html) - PHP Documentation

Health check API:
-------------------
- https://sales.api.travelinsides.com/health-check - Prod
- https://stage-sales-api.travel-dev.com/health-check - Stage
- https://api-sales.dev.travelinsides.com/health-check - Dev

Success-Response (JSON format):
HTTP/1.1 200 OK
{
    "mysql": true,
    "postgresql": true,
    "redis": true
}
If one or more components self-check fails then corresponding field return value "false" and HTTP status is 503 Service Unavailable.
If app config param "apiHealthCheck" username is not empty base auth then API call requires Basic Auth with username/password from this config param.


Generate API Documentation (apiDoc):

 ```
composer apidoc-gen
 ```
it runs these commands:
 ```
 sudo apidoc -c ./apidoc.json -i "./webapi/modules/" -i "./webapi/controllers/" -o "./webapi/web/doc" -f ".*\\.php$"
 sudo apidoc -c ./apidoc.json -i "./webapi/modules/" -i "./webapi/controllers/" -o "./webapi/web/doc2/" -t ./webapi/web/apidoc/template2 -f ".*\\.php$"
 ```

Api Example:
-------------------

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

Filebeat for ELK
-------------------

Filebeat on Ubuntu:
```
wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add -
sudo apt-get install apt-transport-https
echo "deb https://artifacts.elastic.co/packages/6.x/apt stable main" | sudo tee -a /etc/apt/sources.list.d/elastic-6.x.list
sudo apt-get update && sudo apt-get install filebeat
sudo update-rc.d filebeat defaults 95 10

```
Filebeat on CentOS:
```
sudo rpm --import https://packages.elastic.co/GPG-KEY-elasticsearch
sudo nano /etc/yum.repos.d/elastic.repo

        [elastic-6.x]
        name=Elastic repository for 6.x packages
        baseurl=https://artifacts.elastic.co/packages/6.x/yum
        gpgcheck=1
        gpgkey=https://artifacts.elastic.co/GPG-KEY-elasticsearch
        enabled=1
        autorefresh=1
        type=rpm-md

sudo yum install filebeat
sudo chkconfig --add filebeat

```
Copy config file ```./filebeat.yml``` to ```/etc/filebeat/filebeat.yml```
Check Filebeat
```
sudo service filebeat restart
sudo service filebeat status
sudo tail -f /var/log/filebeat/filebeat
``` 

CRONs
-------------------
```
#*/17  *  *  *  *     run-this-one php /var/www/sale/yii monitor-flow/follow-up
*/15  *  *  *  *     run-this-one php /var/www/sale/yii monitor-flow/on-wake
*/20  *  *  *  *     run-this-one php /var/www/sale/yii monitor-flow/watch-dog-decline-quote
*/3   *  *  *  *     run-this-one php /var/www/sale/yii lead/update-ip-info
10   0  *  *  *     run-this-one php /var/www/sale/yii lead/update-by-tasks
30   0  *  *  *     run-this-one php /var/www/sale/yii db/update-airline-cabin-classes
40   0  1  *  *     php /var/www/sale/yii kpi/calculate-salary
*/5 * * * *         php /var/www/sale/yii logger/format-log-managed-attr
*/5 * * * *         php /var/www/sale/yii lead/return-lead-to-ready
10   1-3  *  *  *     php /var/www/sale/yii service/update-currency
*/1  *  *  *  *     php /var/www/sale/yii db/compress-email 
45 * * * *         php /var/www/sale/yii qa-task/lead-processing-quality
*/4 * * * *     php /var/www/sale/yii service/send-sms
*/10   *  *  *  *   run-this-one php /var/www/sale/yii call/terminator
0 9 27 * * php /var/www/sale/yii postgres-db/create-chat-message-partition
*/3 * * * *         php /var/www/sale/yii user-monitor/logout
*/1 * * * *   run-this-one php /var/www/sale/yii client-chat/idle
*/1 * * * *   run-this-one php /var/www/sale/yii client-chat/hold-to-progress
30 4 * * * php /var/www/sale/yii call-report/priceline
* * * * *   php /var/www/sale/yii user/update-online-status
0 0 1 * *   php /var/www/sale/yii client-chat/refresh-rocket-chat-user-token
0 8 * * 1   php /var/www/sale/yii call-report/priceline-weekly
```

```
sudo chmod 777 /var/spool/cron/crontabs/root
```

MemCache
```text
sudo apt-get install memcached libmemcached-tools
sudo apt-get install -y php-memcached
sudo service php7.3-fpm restart
```

Create a Cert
```text
# https://github.com/loganstellway/self-signed-ssl
./self-signed-tls.sh -c=MD -s=Chisinau -l=Chisinau -o=Kivork -u=Kivork -n=sales.zeit.test -e=alex.connor@techork.com
```

PHPDoc (phpDocumentor)
-------------------
Installation PHIVE (https://phar.io/):
```
wget -O phive.phar https://phar.io/releases/phive.phar
wget -O phive.phar.asc https://phar.io/releases/phive.phar.asc
gpg --keyserver hkps://keys.openpgp.org --recv-keys 0x9D8A98B29B2D5D79
gpg --verify phive.phar.asc phive.phar
chmod +x phive.phar
sudo mv phive.phar /usr/local/bin/phive
```
Install phpDocumentor (https://docs.phpdoc.org/3.0/guide/getting-started/installing.html):
```
phive install phpDocumentor
chmod +x ~/.phive/phars/phpdocumentor-3.1.1.phar 
sudo mv ~/.phive/phars/phpdocumentor-3.1.1.phar /usr/local/bin/phpDocumentor
```

Run DOC generate (config file `./phpdoc.xml`):
```
composer phpdoc
```



GeoIP Installation
-------------------
sudo apt-get install php-geoip

restart php-fpm

Test extension:

Paste the following code in the console

```
php -r "echo geoip_time_zone_by_country_and_region('US', 'CA') . PHP_EOL;"

```

CRYPTO - PgSQL
-------------------
```
CREATE EXTENSION pgcrypto;
SELECT encode(encrypt_iv('Hello!', 'PASSWORD', 'IV-STRING-16', 'aes-cbc'), 'base64');
SELECT convert_from(decrypt_iv(decode('a5S7B5nas5XaWibbuX45AA==','base64'), 'PASSWORD', 'IV-STRING-16', 'aes-cbc'),'SQL_ASCII') AS str;
```


CRYPTO - MySQL
-------------------
```
SET block_encryption_mode = 'aes-256-cbc';
SELECT TO_BASE64(AES_ENCRYPT('Hello!','PASSWORD', 'IV-STRING-16')) AS aes
SELECT AES_DECRYPT(FROM_BASE64('PRZKw4PIlNtSPRFuNWYmbA=='), 'PASSWORD', 'IV-STRING-16') AS aes
```

CRYPTO - PHP
-------------------
```

```

Command must return the time zone of the USA - California ---> America/Los_Angeles


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
