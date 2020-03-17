<?php

use kivork\rbacExportImport\src\rbac\DbManager;
use kivork\rbacExportImport\RbacImportExportModule;
use common\models\Employee;
use modules\flight\FlightModule;
use modules\hotel\HotelModule;
use modules\invoice\InvoiceModule;
use modules\offer\OfferModule;
use modules\order\OrderModule;
use modules\product\ProductModule;
use modules\qaTask\QaTaskModule;
use sales\yii\i18n\Formatter;
use yii\web\JqueryAsset;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use frontend\themes\gentelella\assets\AssetLeadCommunication;
use frontend\themes\gentelella_v2\assets\ThemeAsset;

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
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-crm',
        ],

        'log' => [
            'traceLevel' => 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'logVars' => [],
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403'
                    ],
                    'logVars' => [],
                    'prefix' => function () {
                        $userID = Yii::$app->user->isGuest ? '-' : Yii::$app->user->id;
                        $ip = $_SERVER['REMOTE_ADDR'];
                        return "[frontend][$ip][$userID]";
                    },
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info'],
                    'logVars' => [],
                    'categories' => ['info\*'],
                    'prefix' => function () {
                        $userID = Yii::$app->user->isGuest ? '-' : Yii::$app->user->id;
                        $ip = $_SERVER['REMOTE_ADDR'];
                        return "[frontend][$ip][$userID]";
                    },
                ],
                [
                    'class' => \common\components\logger\AirFileTarget::class,
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403'
                    ],
                    //'logVars' => YII_DEBUG ? ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'] : [],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/stash.log'
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
                    //'sourceLanguage' => 'en-US', // Developer language
                    'sourceMessageTable' => '{{%language_source}}',
                    'messageTable' => '{{%language_translate}}',
                    //'cachingDuration' => 86400,
                    //'enableCaching' => true,
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
			'bundles' => [
				BootstrapAsset::class => [
					'sourcePath' => '@npm/bootstrap/dist',
					'css' => [
						'css/bootstrap.css'
					],
				],
				BootstrapPluginAsset::class => [
					'sourcePath' => '@npm/bootstrap/dist',
					'js' => [
						'js/bootstrap.bundle.js'
					],
					'depends' => [
						JqueryAsset::class,
						\yii\bootstrap4\BootstrapAsset::class,
					],
				],
				AssetLeadCommunication::class => [
					'basePath' => '@webroot',
					'baseUrl' => '@web',
					'js' => [
						'https://cdnjs.cloudflare.com/ajax/libs/scrollup/2.4.1/jquery.scrollUp.min.js',
						'/js/sms_counter.min.js',
					],
					'depends' => [
						ThemeAsset::class,
					]
				]
			],
		],
    ],
    'modules' => [
        'gridview' =>  [
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
            'class'                     => \lajax\translatemanager\Module::class,
            'root'                      => '@common/templates',               // The root directory of the project scan.
            'scanRootParentDirectory'   => true,
            'layout'                    => '@frontend/themes/gentelella_v2/views/layouts/main',         // Name of the used layout. If using own layout use 'null'.
            'allowedIPs'                => ['*'],               // 127.0.0.1 IP addresses from which the translation interface is accessible.
            'roles'                     => [Employee::ROLE_SUPER_ADMIN, Employee::ROLE_ADMIN],               // For setting access levels to the translating interface.
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
        'rbac' => [
            'class' => 'yii2mod\rbac\Module',
            'layout' => '@frontend/themes/gentelella_v2/views/layouts/main',
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


        'flight' => [
            'class' => FlightModule::class,
        ],

        'hotel' => [
            'class' => HotelModule::class,
        ],

        'product' => [
            'class' => ProductModule::class,
        ],

        'offer' => [
            'class' => OfferModule::class,
        ],

        'order' => [
            'class' => OrderModule::class,
        ],

        'invoice' => [
            'class' => InvoiceModule::class,
        ],

        'qa-task' => [
            'class' => QaTaskModule::class,
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
    ],
    'as beforeRequest' => [
        'class' => \frontend\components\UserSiteActivityLog::class,
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