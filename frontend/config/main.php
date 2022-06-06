<?php

use modules\abac\AbacModule;
use kivork\FeatureFlag\FeatureFlagModule;
use src\services\microsoft\Microsoft;
use yii\authclient\clients\Google;
use yii\authclient\Collection;
use common\components\logger\FilebeatTarget;
use common\helpers\LogHelper;
use frontend\assets\groups\BootstrapGroupAsset;
use kivork\rbacExportImport\src\rbac\DbManager;
use kivork\rbacExportImport\RbacImportExportModule;
use common\models\Employee;
use modules\cruise\CruiseModule;
use modules\email\EmailModule;
use modules\fileStorage\FileStorageModule;
use modules\flight\FlightModule;
use modules\attraction\AttractionModule;
use modules\hotel\HotelModule;
use modules\invoice\InvoiceModule;
use modules\offer\OfferModule;
use modules\order\OrderModule;
use modules\product\ProductModule;
use modules\qaTask\QaTaskModule;
use modules\requestControl\RequestControlModule;
use common\components\i18n\Formatter;
use modules\rentCar\RentCarModule;
use yii\log\DbTarget;
use yii\log\FileTarget;
use yii\web\JqueryAsset;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use modules\objectSegment\ObjectSegmentModule;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$appVersion = $params['release']['version'] ?? '';
$gitHash = $params['release']['git_hash'] ?? '';

$bundles = ($params['minifiedAssetsEnabled'] ?? false) ? require __DIR__ . '/assets-bundle.php' : [];
//true(YII_ENV === 'prod' || YII_ENV === 'stage' || (YII_ENV === 'dev' && ($params['minifiedAssetsEnabled'] ?? true))) ? require __DIR__ . '/assets-bundle.php' : [];

return [
    'id' => 'app-frontend',
    'name' => 'Sales CRM',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'layout' => '@frontend/themes/gentelella_v2/views/layouts/main.php',
    'components' => [
        'request' => [
            'baseUrl' => '',
            'csrfParam' => '_csrf-frontend',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'cache' => 'cache',
        ],

        'user' => [
            'identityClass' => \common\models\Employee::class,
            'class' => \frontend\components\User::class,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-crm', 'httpOnly' => true],
            'on afterLogin' => static function ($event) {
                Yii::$app->user->identity->initUserStatus();
            }
        ],


        /*'user2' => [
            'class' => 'webvimark\modules\UserManagement\components\UserConfig',
            'on afterLogin' => function($event) {
                \webvimark\modules\UserManagement\models\UserVisitLog::newVisitor($event->identity->id);
            },
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => false],
        ],*/

        'session' => [
            'name' => 'advanced-crm',
        ],

        'log' => [
            'traceLevel' => 0,
            'targets' => [
                'file' => [
                    'class' => FileTarget::class,
                    'logVars' => [],
                    'levels' => ['error', 'warning'],
                ],
                'db-error' => [
                    'class' => DbTarget::class,
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403',
                        'yii\web\HttpException:400',
                    ],
//                    'logVars' => YII_DEBUG ? ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'] : [],
                    'logVars' => [],
                    'prefix' => static function () {
                        return LogHelper::getFrontendPrefixDB();
                    },
                    'db' => 'db_postgres'
                ],
                'db-info' => [
                    'class' => DbTarget::class,
                    'levels' => ['info'],
                    'logVars' => [],
                    'categories' => ['info\*', 'log\*'],
                    'prefix' => static function () {
                        return LogHelper::getFrontendPrefixDB();
                    },
                    'db' => 'db_postgres'
                ],
                'file-fb-error' => [
                    'class' => FilebeatTarget::class,
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:404',
//                        'yii\web\HttpException:403'
                    ],
                    //'logVars' => YII_DEBUG ? ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'] : [],
                    'logVars' => [],
                    'prefix' => static function () {
                        return LogHelper::getFrontendPrefixData();
                    },
                    'logFile' => '@runtime/logs/stash.log'
                ],
                'file-fb-info' => [
                    'class' => FilebeatTarget::class,
                    'levels' => ['info'],
                    'categories' => ['log\*', 'elk\*'],
                    'logVars' => [],
                    'prefix' => static function () {
                        return LogHelper::getFrontendPrefixData();
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

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => require __DIR__ . '/rules.php',
        ],

        'formatter' => [
            'class' => Formatter::class,
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
                    'sourceLanguage' => 'xx', // 'en-US' Developer language
                    'sourceMessageTable' => '{{%language_source}}',
                    'messageTable' => '{{%language_translate}}',
                    'cachingDuration' => 3600,
                    'enableCaching' => true,
                ],
                'yii2mod.rbac' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@yii2mod/rbac/messages',
                ],
            ],
        ],

        'assetManager' => [
            'forceCopy' => false,
            'appendTimestamp' => false,
            'hashCallback' => static function ($path) use ($appVersion) {
                return hash('md4', $path . $appVersion);
            },
            'bundles' => array_merge($bundles, [
                JqueryAsset::class => [
                    'class' => \frontend\assets\JqueryAsset::class,
                ],

                \yii\bootstrap4\BootstrapAsset::class => [
                    'css' => [],
                    'js' => []
                ],
                \yii\bootstrap4\BootstrapPluginAsset::class => [
                    'class' => BootstrapGroupAsset::class,
                ],

                BootstrapAsset::class => [
                    'class' => BootstrapGroupAsset::class,
                ],

                BootstrapPluginAsset::class => [
                    'class' => BootstrapGroupAsset::class,
                ],

//                AssetLeadCommunication::class => [
//                    'basePath' => '@webroot',
//                    'baseUrl' => '@web',
//                    'js' => [
//                        'https://cdnjs.cloudflare.com/ajax/libs/scrollup/2.4.1/jquery.scrollUp.min.js',
//                        '/js/sms_counter.min.js',
//                    ],
//                    'depends' => [
//                        GentelellaAsset::class
//                    ]
//                ]
            ]),
        ],
        'authClientCollection' => [
            'class' => Collection::class,
            'clients' => [
                'google' => [
                    'class' => Google::class,
                    'clientId' => env('FRONTEND_CONFIG_MAIN_COMPONENTS_AUTHCLIENTCOLLECTION_CLIENTS_GOOGLE_CLIENTID'),
                    'clientSecret' => env('FRONTEND_CONFIG_MAIN_COMPONENTS_AUTHCLIENTCOLLECTION_CLIENTS_GOOGLE_CLIENTSECRET')
                ],
                'microsoft' => [
                    'class' => Microsoft::class,
                    'clientId' => env('FRONTEND_CONFIG_MAIN_COMPONENTS_AUTHCLIENTCOLLECTION_CLIENTS_MICROSOFT_CLIENTID'),
                    'clientSecret' => env('FRONTEND_CONFIG_MAIN_COMPONENTS_AUTHCLIENTCOLLECTION_CLIENTS_MICROSOFT_CLIENTSECRET'),
                    'tenantId' => env('FRONTEND_CONFIG_MAIN_COMPONENTS_AUTHCLIENTCOLLECTION_CLIENTS_MICROSOFT_TENANTID'),
                    'host' => env('FRONTEND_CONFIG_MAIN_COMPONENTS_AUTHCLIENTCOLLECTION_CLIENTS_MICROSOFT_HOST'),
                    'authUrl' => env('FRONTEND_CONFIG_MAIN_COMPONENTS_AUTHCLIENTCOLLECTION_CLIENTS_MICROSOFT_AUTH_URL'),
                    'tokenUrl' => env('FRONTEND_CONFIG_MAIN_COMPONENTS_AUTHCLIENTCOLLECTION_CLIENTS_MICROSOFT_TOKEN_URL'),
                    'apiBaseUrl' => env('FRONTEND_CONFIG_MAIN_COMPONENTS_AUTHCLIENTCOLLECTION_CLIENTS_MICROSOFT_API_BASE_URL'),
                    'scope' => env('FRONTEND_CONFIG_MAIN_COMPONENTS_AUTHCLIENTCOLLECTION_CLIENTS_MICROSOFT_SCOPE')
                ],
            ],
        ]
    ],
    'modules' => [
        'gridview' => [
            'class' => '\kartik\grid\Module'
        ],

        /*'supervisor' => [
            'class'    => 'supervisormanager\Module',
            'authData' => [
                'user'     => 'supervisor',
                'password' => 'Supervisor2019!',
                'url'      => 'http://127.0.0.1:9001/RPC2' // Set by default
            ]
        ],*/

        'translatemanager' => [
            'class' => \lajax\translatemanager\Module::class,
            'root' => [/*'@frontend/views/',*/
                '@frontend/../src/model/clientChat/'],               // The root directory of the project scan.
            'scanRootParentDirectory' => true,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',         // Name of the used layout. If using own layout use 'null'.
            'allowedIPs' => ['*'],               // 127.0.0.1 IP addresses from which the translation interface is accessible.
            'roles' => [Employee::ROLE_SUPER_ADMIN, Employee::ROLE_ADMIN],               // For setting access levels to the translating interface.
            'tmpDir' => '@runtime',         // Writable directory for the client-side temporary language files.
            // IMPORTANT: must be identical for all applications (the AssetsManager serves the JavaScript files containing language elements from this directory).
            'phpTranslators' => ['::t'],             // list of the php function for translating messages.
            //'jsTranslators'             => ['lajax.t', 't'],         // list of the js function for translating messages.
            'patterns' => ['*.php'],   // list of file extensions that contain language elements.
            'ignoredCategories' => ['yii', 'language', 'app', 'database', 'yii2mod.rbac'],             // these categories won't be included in the language database.
            //'onlyCategories'            => ['client-chat'],
            'ignoredItems' => ['config', 'vendor', 'console', 'environments', 'node_modules', 'runtime'],          // these files will not be processed.
            'scanTimeLimit' => null,                // increase to prevent "Maximum execution time" errors, if null the default max_execution_time will be used
            'searchEmptyCommand' => '!',                 // the search string to enter in the 'Translation' search field to find not yet translated items, set to null to disable this feature
            'defaultExportStatus' => 1,                   // the default selection of languages to export, set to 0 to select all languages by default
            'defaultExportFormat' => 'json',              // the default format for export, can be 'json' or 'xml'
            'tables' => [                   // Properties of individual tables
                [
                    'connection' => 'db',                    // connection identifier
                    'table' => '{{%language}}',         // table name
                    'columns' => ['name', 'name_ascii'],   //names of multilingual fields
                    //'category'      => 'db',// the category is the database table name
                ],

            ],

            'scanners' => [ // define this if you need to override default scanners (below)
                '\lajax\translatemanager\services\scanners\ScannerPhpFunction',
                '\lajax\translatemanager\services\scanners\ScannerPhpArray',
                //'\lajax\translatemanager\services\scanners\ScannerJavaScriptFunction',
                //'\lajax\translatemanager\services\scanners\ScannerDatabase',
                //\common\components\ScannerTwigFunction::class
            ],

            //'googleApiKey'              => 'AIzaSyCBz5uH4JyegEa_vqN_OGJCORq-UpkmTiQ',
        ],
        'rbac' => [
            'class' => 'yii2mod\rbac\Module',
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
            'as access' => [
                'class' => yii2mod\rbac\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Employee::ROLE_SUPER_ADMIN],
                    ]
                ]
            ],
            'viewPath' => '@frontend/views/rbac',
        ],

        'attraction' => [
            'class' => AttractionModule::class,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
        ],

        'flight' => [
            'class' => FlightModule::class,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
        ],

        'hotel' => [
            'class' => HotelModule::class,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
        ],

        'product' => [
            'class' => ProductModule::class
        ],

        'request-control' => [
            'class' => RequestControlModule::class,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
        ],

        'offer' => [
            'class' => OfferModule::class,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
        ],

        'order' => [
            'class' => OrderModule::class,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
        ],

        'abac' => [
            'class' => AbacModule::class,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
        ],

        'object-segment' => [
            'class' => ObjectSegmentModule::class,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
        ],

        'flag' => [
            'class' => FeatureFlagModule::class,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
            'roles' => ['admin', 'superadmin']
        ],

        'smart' => [
            'class' => \kivork\search\SearchModule::class,
            'Cid' => 'SAL103'
        ],

        'invoice' => [
            'class' => InvoiceModule::class,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
        ],

        'qa-task' => [
            'class' => QaTaskModule::class,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
        ],

        'shift' => [
            'class' => \modules\shiftSchedule\ShiftScheduleModule::class,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
        ],

        'task' => [
            'class' => \modules\taskList\TaskListModule::class,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
        ],
        'rbac-import-export' => [
            'class' => RbacImportExportModule::class,
            'as access' => [
                'class' => yii2mod\rbac\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Employee::ROLE_SUPER_ADMIN],
                    ]
                ]
            ],
            'components' => [
                'authManager' => [
                    'class' => DbManager::class
                ]
            ]
        ],
        'mail' => [
            'class' => EmailModule::class,
        ],
        'virtual-cron' => [
            'class' => \kivork\VirtualCron\VirtualCronModule::class,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud.php',
            'queueName' => 'queue_virtual_cron', //Yii:$app->queue_cron
            'userTable' => 'employees',
            'userIdColumn' => 'id',
            'userClass' => '\common\models\Employee',
            'userFieldDisplay' => 'username', // for gridview, detail vew
            'cronTable' => 'cron_scheduler', //schedulers list table name
            'roles' => ['admin', 'superadmin'], //for roles can manage module
        ],
        'file-storage' => [
            'class' => FileStorageModule::class,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
        ],
        'rent-car' => [
            'class' => RentCarModule::class,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
        ],
        'cruise' => [
            'class' => CruiseModule::class,
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main_crud',
        ],
        'smart-search' => [
            'class' => \kivork\search\SearchModule::class,
        ],
    ],
    'as beforeRequest' => [
        'class' => \frontend\components\UserSiteActivityLog::class,
    ],
    'as access' => [
        'class' => 'yii\filters\AccessControl',
        'except' => ['site/login', 'site/step-two', 'site/captcha', 'site/error', 'site/auth', 'site/auth-step-two', 'application-status/*'],
        'rules' => [
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
    'container' => [
        'definitions' => [
            yii\grid\GridView::class => [
                'options' => ['class' => 'table-responsive'],
                //'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
            ],
            \yii\widgets\LinkPager::class => \yii\bootstrap4\LinkPager::class,
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
