# CRM Sale
Customer relationship management (CRM) is a technology for managing all your company’s relationships and interactions with customers and potential customers.
## Installation:
Minimal Installation setup for local / stage / development server:
(full toolkit setup documentation located at https://traveldev.atlassian.net/wiki/spaces/SLS/pages/1584791640/ )

-------------------
1). Install PHP 7.4 and redis-server :
```sh
sudo apt-get install php7.4 php7.4-fpm php-pear php-imagick php7.4-intl php7.4-zip php7.4-curl php7.4-gd php7.4-mysql php7.4-xml php7.4-mbstring php7.4-pgsql php7.4-xmlrpc php7.4-sqlite3 php7.4-soap php7.4-tidy php7.4-pspell php7.4-imap php7.4-bcmath php-redis php-ssh2
```
Or Install PHP 8.0 and redis-server
```sh
sudo apt-get install php8.0 php8.0-dev php8.0-fpm php-pear php-imagick php8.0-intl php8.0-zip php8.0-curl php8.0-gd php8.0-mysql php8.0-xml php8.0-mbstring php8.0-pgsql php8.0-xmlrpc php8.0-sqlite3 php8.0-soap php8.0-tidy php8.0-pspell php8.0-imap php8.0-bcmath php-redis php-ssh2

# set default php CLI version
sudo update-alternatives --config php
```

### Install Swoole 4.4.16 for PHP7.4
1.1) INSTALL swoole 4.4.16 & ext-async v4.4.16 for swoole_redis
```sh
cd ~
wget https://github.com/redis/hiredis/archive/v0.14.1.tar.gz && tar zxvf v0.14.1.tar.gz
cd hiredis-0.14.1/
make -j && sudo make install
sudo ldconfig
cd ..
 
git clone https://github.com/swoole/swoole-src
cd swoole-src/
git checkout tags/v4.4.16
phpize8.0 && ./configure
make -j 4 && sudo make install
 
git clone https://github.com/swoole/ext-async
cd ext-async/
git checkout tags/v4.4.16
phpize && ./configure
make -j 4 && sudo make install
``` 

Add this in “Dynamic Extensions” section if php-cli config file located at  `/ect/php/7.4/cli/php.ini`
```
extension=swoole
extension=swoole_async
```
### Install Swoole 4.8.9 for PHP8.0
```sh
cd ~
sudo rm -r hiredis 
git clone https://github.com/redis/hiredis && cd hiredis/ 
git checkout tags/v1.0.2
make -j && sudo make install
sudo ldconfig
cd ..

sudo rm -r swoole-src 
git clone https://github.com/swoole/swoole-src
cd swoole-src/
git checkout tags/v4.8.9
phpize8.0 && ./configure
make -j 4 && sudo make install
```
Add this in “Dynamic Extensions” section if php-cli config file located at  `/ect/php/8.0/cli/php.ini`
```
extension=swoole
```


2). Composer INSTALL:
```sh
composer install
```
3). Select ENVIRONMENT:
```sh
./init #(select ENV)
```

### Nginx configuration
1. Copy example config file ./nginx.conf from project folder to your nginx configs directory usually located at `/etc/nginx/sites-available/`;
2. (Optional) You can edit our test hosts to anything else (if you want);
3. Generate self-signed SSL certificate (for example you can use certutil or openssl) and edit in nginx config their file names for 2 hosts (add them to `/etc/hosts` too):
   - "crm.office.test"
   - "api.crm.office.test"

4. Also edit this parameter in nginx config:
```
#for API endpoint
location ~ \.php$ {
    ...
    fastcgi_read_timeout 90s;
}
```

### Migration (RBAC + LOG):
```sh
./yii migrate --migrationPath=@yii/rbac/migrations/
./yii migrate --migrationPath=@yii/log/migrations/
./yii migrate
```

Create MySQL Dump:
```sh
mysqldump -Q -c -e -v -u USER -p DATABASE | gzip > /var/www/backups/sql.gz
gunzip < /var/www/sale/sql.gz | mysql -v -u USER -pPASSWORD DATABASE
gunzip < /var/www/sale/sql.gz | time mysql -u USER -pPASSWORD DATABASE --force
mysqlshow -u USER -pPASSWORD DATABASE
```
Create Pg Dump:
```sh
pg_dump -h localhost --username=postgres -Fc sales3 > /home/user/db.dump
```

edit /etc/mysql/conf.d/mysql.cnf
``` 
[mysqld]
sql_mode=only_full_group_by
```
```sh
sudo service mysql restart
```

## Install Supervisor
 ```sh
sudo apt-get install supervisor
sudo nano /etc/supervisor/supervisord.conf
 ```
Update global config file (code):
```
[include]
files = /var/www/.../common/config/supervisor/*.conf
```

Create supervisor config file (rename to socket-server.conf):
```sh
/var/www/.../common/config/supervisor/socket-server.conf.txt
```

Create supervisor config file (rename to queue-email-job.conf):
```sh
/var/www/.../common/config/supervisor/queue-email-job.conf.txt
```

Start supervisor service:
 ```sh
sudo service supervisor start (OR sudo /etc/init.d/supervisor restart)
#sudo apt-get install php-xmlrpc -y (optional)
 ``` 


### Beanstalk:
[Beanstalk driver for queue](https://github.com/yiisoft/yii2-queue/blob/master/docs/guide-ru/driver-beanstalk.md)
```php
<?php
// example in yii components
'queue_email_job' => [
    'class' => \yii\queue\beanstalk\Queue::class,
    'host' => 'localhost',
    'port' => 11300,
    'tube' => 'queue_email_job',
],
```

*Install on Ubuntu:*
```sh
sudo apt install beanstalkd
```
*Install on Centos:*
```sh
yum install beanstalkd
```

*Run service:*
```sh
service beanstalkd start
```

### Centrifugo Server
*Install on Ubuntu:*

https://centrifugal.github.io/centrifugo/server/install/
```sh
# in .. config/supervisor rename file centrifugo.conf.txt
sudo service supervisor restart
```
END of installation. Now frontend and api must respond. 


## DOCUMENTATION

### Prod Kiv Host:
- [sales.travelinsides.com](https://sales.travelinsides.com) - Frontend
- [sales.api.travelinsides.com](https://sales.api.travelinsides.com) - API
- [sales.api.travelinsides.com/doc/index.html](https://sales.api.travelinsides.com/doc/index.html) - API Documentation
- [sales.api.travelinsides.com/phpdoc/index.html](https://sales.api.travelinsides.com/phpdoc/index.html) - PHP Documentation


### Prod GTT Host:
- [crm.gttglobal.com](https://crm.gttglobal.com) - Frontend
- [crm-api.gttglobal.com](https://crm-api.gttglobal.com) - API
- [crm-api.gttglobal.com/doc/index.html](https://crm-api.gttglobal.com/doc/index.html) - API Documentation

### Stage Host:
- [stage-sales.travel-dev.com](https://stage-sales.travel-dev.com) - Frontend
- [stage-sales-api.travel-dev.com](https://stage-sales-api.travel-dev.com) - API
- [stage-sales-api.travel-dev.com/doc/index.html](https://stage-sales-api.travel-dev.com/doc/index.html) - API Documentation
- [stage-sales-api.travel-dev.com/phpdoc/index.html](https://stage-sales-api.travel-dev.com/phpdoc/index.html) - PHP Documentation

### Dev Host:
- [crm.dev.travel-dev.com](https://crm.dev.travel-dev.com) - Frontend
- [api.crm.dev.travel-dev.com](https://api.crm.dev.travel-dev.com) - API
- [api.crm.dev.travel-dev.com/doc/index.html](https://api.crm.dev.travel-dev.com/doc/index.html) - API Documentation
- [api.crm.dev.travel-dev.com/phpdoc/index.html](https://api.crm.dev.travel-dev.com/phpdoc/index.html) - PHP Documentation

### Health check API:
```
https://{API_HOST}/health-check
```

Success-Response (JSON format):
HTTP/1.1 200 OK
```json
{
    "mysql": true,
    "postgresql": true,
    "redis": true
}
```
- If one or more components self-check fails then corresponding field return value "false" and HTTP status is 503 Service Unavailable.
- If app config param "apiHealthCheck" username is not empty base auth then API call requires Basic Auth with username/password from this config param.


Generate API Documentation (apiDoc):

```sh
composer apidoc-gen
```
it runs these commands:
```sh
sudo apidoc -c ./apidoc.json -i "./webapi/modules/" -i "./webapi/controllers/" -o "./webapi/web/doc" -f ".*\\.php$"
sudo apidoc -c ./apidoc.json -i "./webapi/modules/" -i "./webapi/controllers/" -o "./webapi/web/doc2/" -t ./webapi/web/apidoc/template2 -f ".*\\.php$"
```

### Api Example:


Documentation is at [docs/guide/README.md](docs/guide/README.md).

### Test Code - Migration:
`common/config/test-local.php`
```php
<?php
'db' => [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=localhost;port=5432;dbname=test-sales',
    'username' => 'postgres',
    'password' => 'root',
    'charset' => 'utf8',
    'enableSchemaCache' => false,
],
```
Migrate command:
```sh
./yii_test migrate --migrationPath=vendor/webvimark/module-user-management/migrations/
./yii_test migrate/up --migrationPath=@vendor/lajax/yii2-translate-manager/migrations
./yii_test migrate --migrationPath=@yii/log/migrations/
```

[https://codeception.com/docs/02-GettingStarted](https://codeception.com/docs/02-GettingStarted#Generators)

* generate acceptance ```../vendor/bin/codecept generate:cest acceptance Index```
* build test ```vendor/bin/codecept build```
* run custom test  ```../vendor/bin/codecept run tests/acceptance/IndexCest.php```
* run test ```vendor/bin/codecept run```


### PhantomJS
* Download & install - [http://phantomjs.org/download.html](http://phantomjs.org/download.html)
* Run  ```phantomjs --webdriver=4444```


WebSocket Server (https://github.com/walkor/Workerman):
```sh
sudo php console/socket-server.php start
sudo php console/socket-server.php stop
sudo php console/socket-server.php restart
sudo php console/socket-server.php reload
```
Check process by PORT
```sh
sudo netstat -tulpn| grep :8080
```

### Filebeat for ELK
Filebeat on Ubuntu:
```sh
wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add -
sudo apt-get install apt-transport-https
echo "deb https://artifacts.elastic.co/packages/6.x/apt stable main" | sudo tee -a /etc/apt/sources.list.d/elastic-6.x.list
sudo apt-get update && sudo apt-get install filebeat
sudo update-rc.d filebeat defaults 95 10
```
Filebeat on CentOS:
```sh
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
```sh
sudo service filebeat restart
sudo service filebeat status
sudo tail -f /var/log/filebeat/filebeat
``` 

### CRONs
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

```sh
sudo chmod 777 /var/spool/cron/crontabs/root
```

#### install MemCache
```sh
sudo apt-get install memcached libmemcached-tools
sudo apt-get install -y php-memcached
sudo service php7.3-fpm restart
```

#### Create a Cert
```sh
# https://github.com/loganstellway/self-signed-ssl
./self-signed-tls.sh -c=MD -s=Chisinau -l=Chisinau -o=Kivork -u=Kivork -n=sales.zeit.test -e=alex.connor@techork.com
```

### PHPDoc (phpDocumentor)
Installation PHIVE (https://phar.io/):
```sh
wget -O phive.phar https://phar.io/releases/phive.phar
wget -O phive.phar.asc https://phar.io/releases/phive.phar.asc
gpg --keyserver hkps://keys.openpgp.org --recv-keys 0x9D8A98B29B2D5D79
gpg --verify phive.phar.asc phive.phar
chmod +x phive.phar
sudo mv phive.phar /usr/local/bin/phive
```
Install phpDocumentor (https://docs.phpdoc.org/3.0/guide/getting-started/installing.html):
```sh
phive install phpDocumentor
chmod +x ~/.phive/phars/phpdocumentor-3.1.1.phar 
sudo mv ~/.phive/phars/phpdocumentor-3.1.1.phar /usr/local/bin/phpDocumentor
```

Run DOC generate (config file `./phpdoc.xml`):
```sh
composer phpdoc
```

### GeoIP Installation

```sh
sudo apt-get install php-geoip
```
- restart php-fpm

Test extension:
- Paste the following code in the console

```sh
php -r "echo geoip_time_zone_by_country_and_region('US', 'CA') . PHP_EOL;"
```
- Command must return the time zone of the USA - California ---> America/Los_Angeles

### CRYPTO
PgSQL Example:
```sql
CREATE EXTENSION pgcrypto;
SELECT encode(encrypt_iv('Hello!', 'PASSWORD', 'IV-STRING-16', 'aes-cbc'), 'base64');
SELECT convert_from(decrypt_iv(decode('a5S7B5nas5XaWibbuX45AA==','base64'), 'PASSWORD', 'IV-STRING-16', 'aes-cbc'),'SQL_ASCII') AS str;
```
MySQL Example:
```sql
SET block_encryption_mode = 'aes-256-cbc';
SELECT TO_BASE64(AES_ENCRYPT('Hello!','PASSWORD', 'IV-STRING-16')) AS aes
SELECT AES_DECRYPT(FROM_BASE64('PRZKw4PIlNtSPRFuNWYmbA=='), 'PASSWORD', 'IV-STRING-16') AS aes
```

## Application Information
- [Summary Information](/guides/content/application-info.md)
- - [General Information](/guides/content/application-general-info.md)
- - [Third-party dependencies](/guides/content/application-dependencies.md) (libraries / projects / services)
## License
