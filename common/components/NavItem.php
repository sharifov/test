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
                        'url' => ['site/index']
                    ],
                    [
                        'label' => '<i class="fa fa-user"></i> Users',
                        'url' => ['employee/list']
                    ],
                ];


                if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
                    $items[] = ['label' => '<i class="fa fa-users"></i> User Groups', 'url' => ['user-group/index']];
                    $items[] = ['label' => '<i class="fa fa-users"></i> User Groups Assignments', 'url' => ['user-group-assign/index']];
                    $items[] = ['label' => '<i class="fa fa-users"></i> User Params', 'url' => ['user-params/index']];
                }


                $items[] = ['label' => '<i class="fa fa-plane"></i> Flight Segments', 'url' => ['lead-flight-segment/index']];

                $items[] = ['label' => '<i class="fa fa-quora"></i> Quote List', 'url' => ['quotes/index']];

                $items[] = ['label' => '<i class="fa fa-dollar"></i> Quote Price List', 'url' => ['quote-price/index']];
            }

            $items[] = ['label' => '<i class="fa fa-search"></i> Search Leads', 'url' => ['leads/index']];


            if (in_array(Yii::$app->user->identity->role, ['admin', 'supervision'])) {

                $items[] = ['label' => '<i class="glyphicon glyphicon-export"></i> Export Leads', 'url' => ['leads/export']];
                $items[] = ['label' => '<i class="fa fa-copy"></i> Duplicate Leads', 'url' => ['leads/duplicate']];
                $items[] = ['label' => '<i class="fa fa-users"></i> Stats Agents & Leads', 'url' => ['report/agents']];
                $items[] = ['label' => '<i class="fa fa-list"></i> Lead Status History', 'url' => ['lead-flow/index']];
            }


            $menuItems[] = [
                'label' => ' <i class="fa fa-bars"></i> Menu',
                'options' => ['class' => (in_array(Yii::$app->controller->action->uniqueId, ['site/index', 'employee/list', 'lead-flight-segment/index', 'quotes/index', 'quote-price/index', 'leads/index']) ? 'active' : '')],
                'items' => $items,
            ];


            $menuItems[] = [
                'label' => '<i class="fa fa-bar-chart"></i> Reports',
                'linkOptions' => [
                    'class' => 'dropdown-toggle',
                    'data-toggle' => 'dropdown'
                ],
                'options' => ['class' => (Yii::$app->controller->action->uniqueId == 'report/sold' ? 'active' : '')],
                'itemsOptions' => ['class' => 'dropdown-submenu'],
                'submenuOptions' => ['class' => 'dropdown-menu'],
                'items' => [
                    [
                        'label' => '<i class="fa fa-bar-chart"></i> Sold',
                        'url' => ['report/sold']
                    ],
                ]
            ];



            if (!\in_array(Yii::$app->user->identity->role, ['agent', 'coach'])) {


                $arrayItems = [
                    [
                    'label' => '<i class="fa fa-product-hunt"></i> Projects',
                    'url' => ['settings/projects']
                    ],
                    [
                        'label' => '<i class="fa fa-plane"></i> Airlines',
                        'url' => ['settings/airlines']
                    ],
                    [
                        'label' => '<i class="fa fa-plane"></i> Airports',
                        'url' => ['settings/airports']
                    ],
                    /*[
                        'label' => 'Logging',
                        'url' => ['settings/logging']
                    ],*/
                    [
                        'label' => '<i class="fa fa-users"></i> API Users',
                        'url' => ['api-user/index']
                    ],
                    [
                        'label' => '<i class="fa fa-user-secret"></i> ACL',
                        'url' => ['settings/acl']
                    ],
                    [
                        'label' => '<i class="fa fa-list"></i> Tasks',
                        'url' => ['task/index']
                    ],
                    [
                        'label' => '<i class="fa fa-list"></i> Lead Tasks',
                        'url' => ['lead-task/index']
                    ],
                ];

                $menuItems[] = [
                    'label' => '<i class="fa fa-cog"></i> Settings',
                    'options' => ['class' => (in_array(Yii::$app->controller->action->uniqueId, ['settings/projects', 'settings/airlines', 'settings/airports', 'api-user/index', 'settings/acl', 'task/index', 'lead-task/index']) ? 'active' : '')],
                    'items' => $arrayItems
                ];
            }

            if (Yii::$app->user->identity->role != 'coach') {
                $badges = Lead::getBadgesSingleQuery();
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


            if (\in_array(Yii::$app->user->identity->role, ['admin'])) {

                //$systemLogsCount = <span class="badge badge-warning">'. (\backend\models\Log::find()->where("log_time BETWEEN ".strtotime(date('Y-m-d'))." AND ".strtotime(date('Y-m-d H:i:s')))->count()) .'</span>;
                // $apiLogsCount = <span class="badge badge-warning">'. (\common\models\ApiLog::find()->where("DATE(al_request_dt) = DATE(NOW())")->count()) .'</span>

                $menuItems[] = [
                    'label' => '<i class="fa fa-list"></i> Logs',
                    'options' => ['class' => (in_array(Yii::$app->controller->action->uniqueId, ['api-log/index', 'log/index']) ? 'active' : '')],
                    'items' => [

                        [
                            'label' => '<i class="fa fa-bars"></i> API Logs',
                            'url' => ['api-log/index']
                        ],

                        [
                            'label' => '<i class="fa fa-bars"></i> System Logs',
                            'url' => ['log/index']
                        ],
                    ],
                ];
            }


            $menuItems[] = Html::a('Create Lead', ['lead/create'], ['class' => 'btn btn-xs btn-primary', 'style' => 'margin-top: 8px']);


        }
    }

    private static function isActive($type)
    {
        if ($type == Yii::$app->request->get('type') &&
            Yii::$app->controller->action->id == 'queue'
        ) {
            return 'active';
        }elseif($type == 'sold' && Yii::$app->controller->action->id == 'sold'){
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