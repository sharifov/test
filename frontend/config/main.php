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


        /*'user2' => [
            'class' => 'webvimark\modules\UserManagement\components\UserConfig',
            'on afterLogin' => function($event) {
                \webvimark\modules\UserManagement\models\UserVisitLog::newVisitor($event->identity->id);
            },
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => false],
        ],*/

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
                    'pattern' => 'queue/<type:(inbox|follow-up|processing|processing-all|booked|trash)>',
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
                [
                    'pattern' => 'lead/get-salary/<dateparam>',
                    'route' => 'lead/get-salary',
                ],
                [
                    'pattern' => 'queue/sold',
                    'route' => 'lead/sold',
                ],
            ],
        ],

        'formatter' => [
            'dateFormat' => 'php:d-M-Y', //'dd.MM.yyyy',
            'datetimeFormat' => 'php:d-M-Y [H:i]',
            'timeFormat' => 'php:H:i',
            //'decimalSeparator' => ',',
            //'thousandSeparator' => ' ',
            //'currencyCode' => 'USD',
        ],

        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource'
                ],
            ],
        ],
    ],
    'modules' => [
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ],
        'user-management' => [
            'class' => 'webvimark\modules\UserManagement\UserManagementModule',

            // 'enableRegistration' => true,

            // Add regexp validation to passwords. Default pattern does not restrict user and can enter any set of characters.
            // The example below allows user to enter :
            // any set of characters
            // (?=\S{8,}): of at least length 8
            // (?=\S*[a-z]): containing at least one lowercase letter
            // (?=\S*[A-Z]): and at least one uppercase letter
            // (?=\S*[\d]): and at least one number
            // $: anchored to the end of the string

            //'passwordRegexp' => '^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$',
            'user_table' => '{{%employees}}',


            // Here you can set your handler to change layout for any controller or action
            // Tip: you can use this event in any module
            'on beforeAction'=>function(yii\base\ActionEvent $event) {
                if ( $event->action->uniqueId === 'user-management/auth/login' ) {
                    $event->action->controller->layout = '@frontend/themes/gentelella/views/layouts/login.php'; //loginLayout.php
                };
            },
        ],
    ],
    'as beforeRequest' => [
        'class' => 'common\components\EmployeeActivityLogging',
    ],
    'container' => [
        'definitions' => [
            yii\grid\GridView::class => [
                'options' => ['class' => 'table-responsive'],
                //'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
            ],
        ],
    ],

    /*'view' => [
        'theme' => [
            'basePath' => '@frontend/themes/gentelella',
            'baseUrl' => '@web/themes/gentelella',
            'pathMap' => [
                '@app/views' => '@app/themes/gentelella',
            ],
        ],
    ],*/

    'params' => $params,
];
