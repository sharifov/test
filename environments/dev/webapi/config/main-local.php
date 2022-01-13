<?php

return [
    'components' => [
        'request' => [
            'cookieValidationKey' => env('WEBAPI_CONFIG_MAIN_COMPONENTS_REQUEST_COOKIEVALIDATIONKEY'),
        ],
        'log' => [
            'targets' => [
                [
                    'class' => \kivork\mattermostLogTarget\Target::class,
                    'logVars' => [],
                    //'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'],
                    'levels' => ['error'],
                    'prefix' => static function () {
                        $userID = isset(Yii::$app->user) ? (Yii::$app->user->isGuest ? '-' : Yii::$app->user->id) : '';
                        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
                        return "[webapi][$ip][$userID]";
                    },
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403',
                        'yii\web\HttpException:401'
                    ],
                    'filters' => [
                        'yii\web\BadRequestHttpException: Unable to verify your data submission'
                    ],
                    'containerSettings' =>  [
                        'driver' => [
                            'scheme' => 'https',
                            'basePath' => '/api/v4',
                            'url' => env('COMMON_CONFIG_PARAMS_MATTERMOSTLOGTARGET_CONTAINERSETTINGS_DRIVER_URL'),
                            'login_id' => env('COMMON_CONFIG_PARAMS_MATTERMOSTLOGTARGET_CONTAINERSETTINGS_DRIVER_LOGINID'),
                            'password' => env('COMMON_CONFIG_PARAMS_MATTERMOSTLOGTARGET_CONTAINERSETTINGS_DRIVER_PASSWORD'),
                        ],
                    ],
                    'chanelId' => env('COMMON_CONFIG_PARAMS_MATTERMOSTLOGTARGET_CHANELID'),
                    'handlerClassName' => \common\components\logger\MattermostJobHandler::class,
                    'appPrefix' => 'DEV - CRM - API',
                ],
            ]
        ],
    ],
];
