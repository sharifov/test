<?php

use common\components\logger\FilebeatTarget;
use common\helpers\LogHelper;
use modules\hotel\HotelModule;
use modules\rentCar\RentCarModule;
use yii\log\DbTarget;
use yii\log\FileTarget;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
        ],
        'translate' => \lajax\translatemanager\commands\TranslatemanagerController::class,
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationNamespaces' => [
                //'app\migrations',
                'modules\hotel\migrations',
                'modules\flight\migrations',
                'modules\product\migrations',
                'modules\offer\migrations',
                'modules\order\migrations',
                'modules\invoice\migrations',
                'modules\qaTask\migrations',
                'kivork\VirtualCron\Migrations',
                'modules\attraction\migrations',
                'modules\rentCar\migrations',
                'modules\cruise\migrations',
                'modules\abac\migrations',
                'modules\requestControl\migrations',
                'kivork\FeatureFlag\Migrations',
            ],
        ],

//        'migrate-hotel' => [
//            'class' => 'yii\console\controllers\MigrateController',
//            'migrationNamespaces' => ['modules\hotel\migrations'],
//           // 'migrationTable' => 'migration_module',
//        ],
    ],
    'components' => [
        'log' => [
            'traceLevel' => 0,
            'targets' => [
                'file' => [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
                'db-error' => [
                    'class' => DbTarget::class,
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                    'logVars' => [],
                    'prefix' => static function () {
                        return LogHelper::getConsolePrefixDB();
                    },
                    'db' => 'db_postgres',
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
                        return LogHelper::getConsolePrefixDB();
                    },
                    'db' => 'db_postgres',
                ],
                'file-fb-error' => [
                    'class' => FilebeatTarget::class,
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                    'prefix' => static function () {
                        return LogHelper::getConsolePrefixData();
                    },
                    //'logVars' => YII_DEBUG ? ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'] : [],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/stash.log',
                ],
                'file-fb-log' => [
                    'class' => FilebeatTarget::class,
                    'levels' => ['info'],
                    'categories' => ['log\*', 'elk\*'],
                    'logVars' => [],
                    'prefix' => static function () {
                        return LogHelper::getConsolePrefixData();
                    },
                    'logFile' => '@runtime/logs/stash.log'
                ],
//                'analytics-fb-log' => [
//                    'class' => FilebeatTarget::class,
//                    'levels' => ['info'],
//                    'categories' => ['analytics\*', 'AS\*'],
//                    'logVars' => [],
//                    'prefix' => static function () {
//                        return LogHelper::getAnalyticPrefixData();
//                    },
//                    'logFile' => '@runtime/logs/stash.log'
//                ],
            ],
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\DbMessageSource',
                    'db' => 'db',
                    'sourceLanguage' => 'en-US', // Developer language
                    'sourceMessageTable' => 'language_source',
                    'messageTable' => 'language_translate',
                    'cachingDuration' => 86400,
                    'enableCaching' => true,
                ],
            ],
        ],

        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        /*'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'common\models\Employee',
            'enableSession' => false
        ],*/
        'urlManager' => [
            'scriptUrl' => '/'
        ],

    ],
    'modules' => [
        'translatemanager' => [
            'class'                     => 'lajax\translatemanager\Module',
            'root'                      => '@frontend',               // The root directory of the project scan.
            'scanRootParentDirectory'   => true,
            'layout'                    => '@frontend/themes/gentelella_v2/views/layouts/main',         // Name of the used layout. If using own layout use 'null'.
            'allowedIPs'                => ['*'],               // 127.0.0.1 IP addresses from which the translation interface is accessible.
            //'roles'                     => ['@'],               // For setting access levels to the translating interface.
            'tmpDir'                    => '@runtime',         // Writable directory for the client-side temporary language files.
            // IMPORTANT: must be identical for all applications (the AssetsManager serves the JavaScript files containing language elements from this directory).
            'phpTranslators'            => ['::t'],             // list of the php function for translating messages.
            'jsTranslators'             => ['lajax.t'],         // list of the js function for translating messages.
            'patterns'                  => [/*'*.js',*/'*.php'],   // list of file extensions that contain language elements.
            'ignoredCategories'         => ['yii', 'language', 'app', 'database'],             // these categories won't be included in the language database.
            'ignoredItems'              => ['config', 'vendor', 'console', 'environments', 'node_modules'],          // these files will not be processed.
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

            //'googleApiKey'              => 'AIzaSyCBz5uH4JyegEa_vqN_OGJCORq-UpkmTiQ',
        ],
        'virtual-cron' => [
            'class' => \kivork\VirtualCron\VirtualCronModule::class,
            'queueName' => 'queue_virtual_cron', //Yii:$app->queue_cron
            'userTable' => 'employees',
            'userIdColumn' => 'id',
            'userClass' => '\common\models\Employee',
            'userFieldDisplay' => 'username', // for gridview, detail vew
        ],
        'flag' => [
            'class' => \kivork\FeatureFlag\FeatureFlagModule::class,
//            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
            'user' => [
                'table' => 'employee',
                'idColumn' => 'id',
                'class' => \common\models\Employee::class,
                'fieldDisplay' => 'username',           // for gridview, detail vew
            ],

//            'roles' => ['@', 'admin'],    //for roles can manage module
        ],
        'hotel' => [
            'class' => HotelModule::class,
        ],
        'rent-car' => [
            'class' => RentCarModule::class,
        ],
    ],
    'params' => $params,
];
