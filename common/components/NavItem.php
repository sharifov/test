<?php

namespace common\components;

use common\models\ApiLog;
use common\models\ExpertSale;
use common\models\Lead;
use common\models\SaleSale;
use common\models\SourcePermission;
use common\models\SupportSale;
use common\models\Team;
use common\models\TicketSale;
use common\models\VerificationSale;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

class NavItem
{
    public static function items(&$menuItems)
    {
        if (!Yii::$app->user->isGuest) {

            if (!in_array(Yii::$app->user->identity->role, ['admin', 'supervision'])) {
                $items = [
                    [
                        'label' => 'Dashboard',
                        'url' => ['site/index']
                    ],
                ];
            } else {
                $items = [
                    [
                        'label' => 'Dashboard',
                        'url' => (!strpos(Yii::$app->request->baseUrl, 'admin'))
                            ? ['admin/site/index']
                            : ['site/index']
                    ],
                    [
                        'label' => '<i class="fa fa-user"></i> Employees',
                        'url' => (!strpos(Yii::$app->request->baseUrl, 'admin'))
                            ? ['admin/employee/list']
                            : ['employee/list']
                    ],
                    /*[
                        'label' => 'Settings',
                        'linkOptions' => [
                            'class' => 'dropdown-toggle',
                            'data-toggle' => 'dropdown'
                        ],
                        'itemsOptions' => ['class' => 'dropdown-submenu'],
                        'submenuOptions' => ['class' => 'dropdown-menu'],
                        'items' => [
                            [
                                'label' => 'ACL',
                                'url' => sprintf('%s/admin/settings/acl', Yii::$app->urlManager->getHostInfo())
                            ],
                            [
                                'label' => 'Projects',
                                'url' => sprintf('%s/admin/settings/projects', Yii::$app->urlManager->getHostInfo())
                            ],
                            [
                                'label' => 'Airlines',
                                'url' => sprintf('%s/admin/settings/airlines', Yii::$app->urlManager->getHostInfo())
                            ],
                            [
                                'label' => 'Airports',
                                'url' => sprintf('%s/admin/settings/airports', Yii::$app->urlManager->getHostInfo())
                            ],
                            [
                                'label' => 'Logging',
                                'url' => sprintf('%s/admin/settings/logging', Yii::$app->urlManager->getHostInfo())
                            ],

                            [
                                'label' => 'API Users',
                                'url' => ['/api-user/index']
                            ],

                            [
                                'label' => 'API Logs',
                                'url' => ['/api-log/index']
                            ],
                        ]
                    ],*/
                ];
            }
            $items[] = ['label' => '<i class="fa fa-search"></i> Search Order', 'url' => ['search/index']];
            $items[] = [
                'label' => '<i class="fa fa-bar-chart"></i> Reports',
                'linkOptions' => [
                    'class' => 'dropdown-toggle',
                    'data-toggle' => 'dropdown'
                ],
                'itemsOptions' => ['class' => 'dropdown-submenu'],
                'submenuOptions' => ['class' => 'dropdown-menu'],
                'items' => [
                    [
                        'label' => 'Sold',
                        'url' => sprintf('%s/report/sold', Yii::$app->urlManager->getHostInfo())
                    ],
                ]
            ];
            $menuItems[] = [
                'label' => '<i class="fa fa-bars"></i> Menu',
                'items' => $items,
            ];

            if (!in_array(Yii::$app->user->identity->role, ['agent', 'coach'])) {
                $menuItems[] = [
                    'label' => '<i class="fa fa-cog"></i> Settings',
                    'items' => [

                        [
                            'label' => '<i class="fa fa-product-hunt"></i> Projects',
                            'url' => sprintf('%s/admin/settings/projects', Yii::$app->urlManager->getHostInfo())
                        ],
                        [
                            'label' => '<i class="fa fa-plane"></i> Airlines',
                            'url' => sprintf('%s/admin/settings/airlines', Yii::$app->urlManager->getHostInfo())
                        ],
                        [
                            'label' => '<i class="fa fa-plane"></i> Airports',
                            'url' => sprintf('%s/admin/settings/airports', Yii::$app->urlManager->getHostInfo())
                        ],
                        /*[
                            'label' => 'Logging',
                            'url' => sprintf('%s/admin/settings/logging', Yii::$app->urlManager->getHostInfo())
                        ],*/
                        [
                            'label' => '<i class="fa fa-users"></i> API Users',
                            'url' => sprintf('%s/admin/api-user/index', Yii::$app->urlManager->getHostInfo())
                        ],
                        [
                            'label' => '<i class="fa fa-user-secret"></i> ACL',
                            'url' => sprintf('%s/admin/settings/acl', Yii::$app->urlManager->getHostInfo())
                        ],

                    ],
                ];
            }


            if (Yii::$app->user->identity->role != 'coach') {
                $badges = Lead::getBadges();
                $menuItems[] = '<li class="' . self::isActive('inbox') . '">'
                    . Html::a('Inbox<span id="inbox-queue" class="badge badge-info">' . $badges['inbox'] . '</span > ', self::getQueueUri('inbox'))
                    . ' </li > ';
                $menuItems[] = '<li class="' . self::isActive('follow-up') . '">'
                    . Html::a('Follow Up<span class="badge badge-success">' . $badges['follow-up'] . '</span > ', self::getQueueUri('follow-up'))
                    . ' </li > ';
                $menuItems[] = '<li class="' . self::isActive('processing') . '">'
                    . Html::a('Processing(Me)<span class="badge badge-warning">' . $badges['processing'] . '</span > ', self::getQueueUri('processing'))
                    . ' </li > ';

                if (Yii::$app->user->identity->role != 'agent') {
                    $menuItems[] = '<li class="' . self::isActive('processing-all') . '">'
                        . Html::a('Processing(All)<span class="badge badge-mint">' . $badges['processing-all'] . '</span > ', self::getQueueUri('processing-all'))
                        . ' </li > ';
                }

                $menuItems[] = '<li class="' . self::isActive('booked') . '">'
                    . Html::a('Booked<span class="badge badge-success">' . $badges['booked'] . '</span > ', self::getQueueUri('booked'))
                    . ' </li > ';
                $menuItems[] = '<li class="' . self::isActive('sold') . '">'
                    . Html::a('Sold<span class="badge badge-success">' . $badges['sold'] . '</span > ', self::getQueueUri('sold'))
                    . ' </li > ';

                if (Yii::$app->user->identity->role != 'agent') {
                    $menuItems[] = '<li class="' . self::isActive('trash') . '">'
                        . Html::a('Trash<span class="badge badge-warning">' . $badges['trash'] . '</span > ', self::getQueueUri('trash'))
                        . ' </li > ';
                }
            }


            if (in_array(Yii::$app->user->identity->role, ['admin', 'supervision'])) {
                $menuItems[] = [
                    'label' => '<i class="fa fa-list"></i> Logs',
                    'items' => [

                        [
                            'label' => '<i class="fa fa-bars"></i> API Logs <span class="badge badge-warning">'. (\common\models\ApiLog::find()->where("DATE(al_request_dt) = DATE(NOW())")->count()) .'</span>',
                            'url' => sprintf('%s/admin/api-log/index', Yii::$app->urlManager->getHostInfo())
                        ],

                        [
                            'label' => '<i class="fa fa-bars"></i> System Logs <span class="badge badge-warning">'. (\backend\models\Log::find()->where("log_time BETWEEN ".strtotime(date('Y-m-d'))." AND ".strtotime(date('Y-m-d H:i:s')))->count()) .'</span>',
                            'url' => sprintf('%s/admin/log/index', Yii::$app->urlManager->getHostInfo())
                        ],
                    ],
                ];
            }


        }
    }

    private static function isActive($type)
    {
        if ($type == Yii::$app->request->get('type') &&
            Yii::$app->controller->action->id = 'queue'
        ) {
            return 'active';
        }
        return '';
    }

    private static function getQueueUri($type)
    {
        return sprintf('%s/queue/%s', Yii::$app->urlManager->getHostInfo(), $type);
    }

    private static function isActiveSubmenu($team, $teamName)
    {
        return ($team == $teamName);
    }
}