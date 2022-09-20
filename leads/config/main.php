<?php

use common\components\logger\FilebeatTarget;
use common\helpers\LogHelper;
use yii\log\FileTarget;
use yii\log\DbTarget;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-leads',
    'basePath' => dirname(__DIR__),
    'homeUrl' => '/',
    'controllerNamespace' => 'leads\controllers',
    'bootstrap' => [
        'log',
    ],
    'language' => 'en',
    'modules' => [
        'v1' => [
            'class' => \leads\modules\v1\Module::class,
            'basePath' => '@leads/modules/v1',
            'controllerNamespace' => 'leads\modules\v1\controllers',
        ],
    ],

    'components' => [
        'defaultRoute' => 'site/index',
        'response' => [
            'class' => 'yii\web\Response',
            'format' => \yii\web\Response::FORMAT_JSON,
        ],
        'request' => [
            'baseUrl' => '',
            'class' => '\yii\web\Request',
            'enableCookieValidation' => false,
            'enableCsrfCookie' => false,
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
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
            ],
        ],
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
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],

    ],

    'params' => $params,
];
