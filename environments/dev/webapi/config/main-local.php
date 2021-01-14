<?php

return [
    'components' => [
        'request' => [
            'cookieValidationKey' => '{{ webapi.config.main.components.request.cookieValidationKey:str }}',
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
                            'url' => '{{ common.config.params.mattermostLogTarget.containerSettings.url:str }}',
                            'login_id' => '{{ common.config.params.mattermostLogTarget.containerSettings.login_id:str }}',
                            'password' => '{{ common.config.params.mattermostLogTarget.containerSettings.password:str }}',
                        ],
                    ],
                    'chanelId' => '{{ common.config.params.mattermostLogTarget.chanelId:str }}',
                    'handlerClassName' => \common\components\logger\MattermostJobHandler::class,
                    'appPrefix' => 'DEV - CRM - API',
                ],
            ]
        ],
    ],
];
