<?php

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
    'bootstrap' => ['log'],
    'language' => 'en',

    'modules' => [
        'v1' => [
            'class' => 'webapi\modules\v1\Module',
            'basePath' => '@webapi/modules/v1',
            'controllerNamespace' => 'webapi\modules\v1\controllers',
        ],
    ],

    'components' => [
        'defaultRoute' => 'site/index',
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
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

        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\GettextMessageSource',
                    'basePath' => '@common/messages',
                    'sourceLanguage' => 'en-US',
                ],
            ],
        ],

        'log' => [
            'traceLevel' => 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    //'logVars' => [],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                    //'logVars' => [],
                    'logVars' => ['_POST', '_GET'],
                    'prefix' => function () {
                        $userID = Yii::$app->user->isGuest ? '-' : Yii::$app->user->id;
                        $ip = $_SERVER['REMOTE_ADDR'];
                        return "[webapi][$ip][$userID]";
                    },
                ],
                [
                    'class' => \yii\log\DbTarget::class,
                    'levels' => ['info'],
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                    'logVars' => [],
                    'categories' => ['info\*'],
                    'prefix' => function () {
                        $userID = Yii::$app->user->isGuest ? '-' : Yii::$app->user->id;
                        $ip = $_SERVER['REMOTE_ADDR'];
                        return "[webapi][$ip][$userID]";
                    },
                ],
                [
                    'class' => \common\components\logger\AirFileTarget::class,
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                    //'logVars' => YII_DEBUG ? ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'] : [],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/stash.log'
                ],
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
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],

    ],

    'params' => $params,
];
