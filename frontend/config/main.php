<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'name'  => 'Sales',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'baseUrl' => '',
            'csrfParam' => '_csrf-frontend',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'user' => [
            'identityClass' => 'common\models\Employee',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-crm', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-crm',
        ],

        'log' => [
            'traceLevel' => 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                    'logVars' => [],
                    'prefix' => function () {
                        $userID = Yii::$app->user->isGuest ? '-' : Yii::$app->user->id;
                        $ip = $_SERVER['REMOTE_ADDR'];
                        return "[frontend][$ip][$userID]";
                    },
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                [
                    'pattern' => 'queue/<type:(inbox|follow-up|processing|processing-all|booked|sold|trash)>',
                    'route' => 'lead/queue',
                ],
                [
                    'pattern' => 'lead/<type:(inbox|follow-up|processing|processing-all|booked|sold|trash)>/<id>',
                    'route' => 'lead/quote',
                ],
                [
                    'pattern' => 'take/<id>',
                    'route' => 'lead/take',
                ],
            ],
        ],

        'formatter' => [
            'dateFormat' => 'php:d-M-Y', //'dd.MM.yyyy',
            'datetimeFormat' => 'php:d-M-Y [H:i]',
            //'decimalSeparator' => ',',
            //'thousandSeparator' => ' ',
            //'currencyCode' => 'USD',
        ],
    ],
    'modules' => [
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ]
    ],
    'as beforeRequest' => [
        'class' => 'common\components\EmployeeActivityLogging',
    ],
    'container' => [
        'definitions' => [
            yii\grid\GridView::class => [
                'options' => ['class' => 'table-responsive'],
                'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
            ],
        ],
    ],
    'params' => $params,
];
