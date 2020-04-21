Example settings for supervisor

[program:QueueGmailDownload]
command=/usr/bin/php /var/www/sales/www/yii queue-gmail-download/listen  --verbose=1 --color=0
stdout_logfile=/var/www/sales/www/console/runtime/queue-gmail-download.log
autostart=true
autorestart=true
user=root
stopsignal=KILL
numprocs=1

-----------

add to config

'components' => [
    'queue_gmail_download' => [
         'class' => \yii\queue\beanstalk\Queue::class,
         'host' => 'localhost',
         'port' => 11300,
         'tube' => 'queue_gmail_download',
    ],
],
'bootstrap' => [
    'queue_gmail_download',
],

-----------

add Gmail APi credentials to params-local file

'gmail_api_project_credentials' => '...',


----------

add folder for migrations

'modules\email\migrations'

----------