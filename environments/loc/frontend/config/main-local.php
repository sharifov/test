<?php

$config = [
    'name' => 'CRM - DEV',
    'components' => [
        'request' => [
            'cookieValidationKey' => env('frontend.config.main.components.request.cookieValidationKey'),
        ],
        'log' => [
            'targets' => [
                [
                    'class' => \kivork\mattermostLogTarget\Target::class,
                    'logVars' => [],
                    'levels' => ['error'],
                    'prefix' => static function () {
                        $userID = isset(Yii::$app->user) ? (Yii::$app->user->isGuest ? '-' : Yii::$app->user->id) : '';
                        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
                        return "[frontend][$ip][$userID]";
                    },
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403'
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
                    'appPrefix' => 'DEV - CRM - Frontend',
                ],
            ]
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
