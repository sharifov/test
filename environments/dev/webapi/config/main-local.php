<?php

return [
    'components' => [
        'request' => [
            'cookieValidationKey' => env('webapi.config.main.components.request.cookieValidationKey'),
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
                            'url' => env('common.config.params.mattermostLogTarget.containerSettings.driver.url'),
                            'login_id' => env('common.config.params.mattermostLogTarget.containerSettings.driver.login_id'),
                            'password' => env('common.config.params.mattermostLogTarget.containerSettings.driver.password'),
                        ],
                    ],
                    'chanelId' => env('common.config.params.mattermostLogTarget.chanelId'),
                    'handlerClassName' => \common\components\logger\MattermostJobHandler::class,
                    'appPrefix' => 'DEV - CRM - API',
                ],
            ]
        ],
    ],
];
