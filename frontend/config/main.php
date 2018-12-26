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
                /*[
                    'pattern' => 'queue/<type:(inbox1|follow-up1|processing1|processing-all|booked1|trash1)>',
                    'route' => 'lead/queue',
                ],*/
                [
                    'pattern' => 'lead/view/<id>',
                    'route' => 'lead/view',
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
                [
                    'pattern' => 'queue/processing',
                    'route' => 'lead/processing',
                ],
                [
                    'pattern' => 'queue/follow-up',
                    'route' => 'lead/follow-up',
                ],
                [
                    'pattern' => 'queue/inbox',
                    'route' => 'lead/inbox',
                ],
                [
                    'pattern' => 'queue/trash',
                    'route' => 'lead/trash',
                ],
                [
                    'pattern' => 'queue/booked',
                    'route' => 'lead/booked',
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
            'translations' => [/*
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource'
                ], */
                '*' => [
                    'class' => 'yii\i18n\DbMessageSource',
                    'db' => 'db',
                    //'sourceLanguage' => 'en-US', // Developer language
                    'sourceMessageTable' => '{{%language_source}}',
                    'messageTable' => '{{%language_translate}}',
                    //'cachingDuration' => 86400,
                    //'enableCaching' => true,
                ],
            ],
        ],
    ],
    'modules' => [
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ],
        'translatemanager' => [
            'class'                     => \lajax\translatemanager\Module::class,
            'root'                      => '@common/templates',               // The root directory of the project scan.
            'scanRootParentDirectory'   => true,
            'layout'                    => '@frontend/themes/gentelella/views/layouts/main',         // Name of the used layout. If using own layout use 'null'.
            'allowedIPs'                => ['*'],               // 127.0.0.1 IP addresses from which the translation interface is accessible.
            'roles'                     => ['@'],               // For setting access levels to the translating interface.
            'tmpDir'                    => '@runtime',         // Writable directory for the client-side temporary language files.
            // IMPORTANT: must be identical for all applications (the AssetsManager serves the JavaScript files containing language elements from this directory).
            'phpTranslators'            => ['Yii::t', 't'],             // list of the php function for translating messages.
            //'jsTranslators'             => ['lajax.t', 't'],         // list of the js function for translating messages.
            'patterns'                  => [/*'*.js',*/'*.twig'],   // list of file extensions that contain language elements.
            'ignoredCategories'         => ['yii', 'language', 'app', 'database'],             // these categories won't be included in the language database.
            'ignoredItems'              => ['config', 'vendor', 'console', 'environments', 'node_modules', 'runtime'],          // these files will not be processed.
            'scanTimeLimit'             => null,                // increase to prevent "Maximum execution time" errors, if null the default max_execution_time will be used
            'searchEmptyCommand'        => '!',                 // the search string to enter in the 'Translation' search field to find not yet translated items, set to null to disable this feature
            'defaultExportStatus'       => 1,                   // the default selection of languages to export, set to 0 to select all languages by default
            'defaultExportFormat'       => 'json',              // the default format for export, can be 'json' or 'xml'
            'tables' => [                   // Properties of individual tables
                [
                    'connection'    => 'db',                    // connection identifier
                    'table'         => '{{%language}}',         // table name
                    'columns'       => ['name', 'name_ascii'],   //names of multilingual fields
                    //'category' => 'database-table-name',// the category is the database table name
                ]
            ],
            'scanners' => [ // define this if you need to override default scanners (below)
                //'\lajax\translatemanager\services\scanners\ScannerPhpFunction',
                //'\lajax\translatemanager\services\scanners\ScannerPhpArray',
                //'\lajax\translatemanager\services\scanners\ScannerJavaScriptFunction',
                //'\lajax\translatemanager\services\scanners\ScannerDatabase',
                \common\components\ScannerTwigFunction::class
            ],

            //'googleApiKey'              => 'AIzaSyCBz5uH4JyegEa_vqN_OGJCORq-UpkmTiQ',
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
