<?php

use modules\email\EmailModule;

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
                'modules\email\migrations',
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
                        //$ip = $_SERVER['REMOTE_ADDR'];
                        return '[console]';
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
                        return '[console]';
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

    ],
    'modules'=>[
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
        'mail' => [
            'class' => EmailModule::class,
        ],
    ],
    'params' => $params,
];
