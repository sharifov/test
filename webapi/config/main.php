<?php

use common\components\logger\FilebeatTarget;
use common\helpers\LogHelper;
use modules\hotel\HotelModule;
use yii\log\FileTarget;
use yii\log\DbTarget;
use webapi\bootstrap\SetUp;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-webapi',
    'basePath' => dirname(__DIR__),
    //'timeZone' => 'Europe/Chisinau',
    'homeUrl'   =>  '/',
    'controllerNamespace' => 'webapi\controllers',
    'bootstrap' => [
        'log',
        SetUp::class,
    ],
    'language' => 'en',

    'modules' => [
        'v1' => [
            'class' => 'webapi\modules\v2\Module',
            'basePath' => '@webapi/modules/v2',
            'controllerNamespace' => 'webapi\modules\v1\controllers',
        ],
        'v2' => [
            'class' => 'webapi\modules\v2\Module',
            'basePath' => '@webapi/modules/v2',
            'controllerNamespace' => 'webapi\modules\v2\controllers',
        ],
        'hotel' => [
            'class' => HotelModule::class,
        ],
        'flag' => [
            'class' => \kivork\FeatureFlag\FeatureFlagModule::class,
        ],
    ],

    'components' => [
        'defaultRoute' => 'site/index',
        'response' => [
            'class' => 'yii\web\Response',
            'format' =>  \yii\web\Response::FORMAT_JSON,
            'on beforeSend' => static function ($event) {
                $response = $event->sender;
                if ($response->data !== null && !empty(Yii::$app->request->get('suppress_response_code'))) {
                    $response->data = [
                        'success' => $response->isSuccessful,
                        'data' => $response->data,
                    ];
                    $response->statusCode = 200;
                }
            },
        ],
        'request' => [
            'cookieValidationKey' => '56781237789a099b20sdfgsdfgsf8hdofiug52bd47cf1d3affc9d4f44e55f84b4652dcc0547adb4f6',
            'baseUrl' => '',
            'class' => '\yii\web\Request',
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],

        'user' => [
            'identityClass' => 'webapi\models\ApiUser',
            'enableAutoLogin' => false,
            'enableSession' => false
        ],

        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'cache' => 'cache',
        ],


        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\DbMessageSource',
                    'db' => 'db',
                    'sourceLanguage' => 'xx', // 'en-US' Developer language
                    'sourceMessageTable' => '{{%language_source}}',
                    'messageTable' => '{{%language_translate}}',
                    'cachingDuration' => 3600,
                    'enableCaching' => true,
                ],
            ],
        ],

        'log' => [
            'traceLevel' => 0,
            'targets' => [
                'file' => [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                    //'logVars' => [],
                ],
                'db-error' => [
                    'class' => DbTarget::class,
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:401'
                    ],
                    'logVars' => [],
//                    'logVars' => ['_POST', '_GET'],
                    'prefix' => static function () {
                        return LogHelper::getWebapiPrefixDB();
                    },
                    'db' => 'db_postgres'
                ],
                'db-info' => [
                    'class' => DbTarget::class,
                    'levels' => ['info'],
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                    'logVars' => [],
                    'categories' => ['info\*', 'log\*'],
                    'prefix' => static function () {
                        return LogHelper::getWebapiPrefixDB();
                    },
                    'db' => 'db_postgres'
                ],
                'file-fb-error' => [
                    'class' => FilebeatTarget::class,
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:401'
                    ],
                    'prefix' => static function () {
                        return LogHelper::getWebapiPrefixData();
                    },
                    //'logVars' => YII_DEBUG ? ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'] : [],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/stash.log'
                ],
                'file-fb-log' => [
                    'class' => FilebeatTarget::class,
                    'levels' => ['info'],
                    'categories' => ['log\*', 'elk\*'],
                    'logVars' => [],
                    'prefix' => static function () {
                        return LogHelper::getWebapiPrefixData();
                    },
                    'logFile' => '@runtime/logs/stash.log'
                ],
//                'analytics-fb-log' => [
//                    'class' => FilebeatTarget::class,
//                    'levels' => ['info'],
//                    'categories' => ['analytics\*', 'AS\*'],
//                    'logVars' => [],
//                    'prefix' => static function () {
//                        return LogHelper::getAnalyticPrefixData();
//                    },
//                    'logFile' => '@runtime/logs/stash.log'
//                ],
                /*[
                    'class'         => \primipilus\log\TelegramTarget::class,
                    'levels'        => ['error'],
                    'timeout'       => 0.4,
                    'token'         => '691964462:AAF1s3rZRFGYQ0k6RTuBGtELacnuSHOyePM',
                    'chatId'        => '270012521',
                    'prefixMessage' => $_SERVER['HTTP_HOST'], //'BookAir Test',
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                    'logVars'       => [],
                    'prefix' => function () {
                        $userID = Yii::$app->user->isGuest ? '-' : Yii::$app->user->id;
                        $ip = $_SERVER['REMOTE_ADDR'];
                        return "[webapi][$ip][$userID]";
                    },
                    //'proxy'         => 'protocol://login:password@host:port',
                ],*/
            ],
        ],
        //'urlFrontendManager' => require(__DIR__ . '/_urlFrontendManager.php'),
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'showScriptName' => false,
            'enablePrettyUrl' => true,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['app'],
                    //'pluralize' => true,
                    //'tokens' => ['{id}' => '<id:\\d+>']
                ],
                'health-check' => 'health/dummy',
                'health-check/metrics' => 'health/dummy',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],

    ],

    'params' => $params,
];
