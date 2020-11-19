<?php

$config = [
    'name' => 'CRM - DEV',
    'components' => [
        'request' => [
            'cookieValidationKey' => '{{ frontend.config.main.components.request.cookieValidationKey:str }}',
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
                            'url' => '{{ frontend.config.main.components.log.targets.containerSettings.url:str }}',
                            'login_id' => '{{ frontend.config.main.components.log.targets.containerSettings.login_id:str }}',
                            'password' => '{{ frontend.config.main.components.log.targets.containerSettings.password:str }}'
                        ],
//                        'guzzle' => [
//                            //put here any options for Guzzle
//                        ]
                    ],
                    'chanelId' => '{{ frontend.config.main.components.log.targets.chanelId:str }}',
                    'handlerClassName' => \common\components\logger\MattermostJobHandler::class,
                    'appPrefix' => 'DEV - CRM - Frontend',
                ],
            ]
        ],
    ],
];

$config['bootstrap'][] = 'debug';
$config['modules']['debug'] = [
    'class' => 'yii\debug\Module',
    'allowedIPs' => ['127.0.0.1', '*']  //allowing ip's
];


/*if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '178.18.45.25', '217.26.162.22']  //allowing ip's
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}*/

return $config;
