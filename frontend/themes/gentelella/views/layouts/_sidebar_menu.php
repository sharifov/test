<?php
/* @var $this \yii\web\View */

use yii\helpers\Html;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;


$isAdmin = Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id);
$isSupervision = Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id);
$isCoach = Yii::$app->authManager->getAssignment('coach', Yii::$app->user->id);



?>
<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

    <div class="menu_section">


        <br>
        <br>
        <br>
        <? /*<div class="text-center">
            <?= Html::a('<i class="fa fa-plus" style="font-size: 10px"></i> Create Lead &nbsp;&nbsp;&nbsp;&nbsp;', ['lead/create'], ['class' => 'btn btn-success btn-xs']) ?>
        </div>*/ ?>

        <?php


        $menuItems = [];

        //\common\components\NavItem::items($menuItems);

        /*echo \yii\bootstrap\Nav::widget([
            'encodeLabels' => false,
            'options' => ['class' => 'nav navbar-nav top-left-menu'],
            'items' => $menuItems,
        ]);*/

        /*NavBar::begin([
            'brandLabel' => 'AIE - '.$host,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
        ]);*/


        //$menuItems[] = ["label" => '<i class="fa fa-home"></i><span>'.Yii::t('menu', 'Home').'</span><small class="label-success label pull-right">new</small>', "url" => "/"];
        $menuItems[] = ['label' => 'Create new Lead', 'url' => ['lead/create'], 'icon' => 'plus'];
        $menuItems[] = ["label" => "Dashboard", "url" => ["/"], "icon" => "area-chart"];

        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
        } else {

            $menuItems[] = ['label' => 'Search Leads', 'url' => ['/leads/index'], 'icon' => 'search'];


            if($isAdmin || $isSupervision) {
                $menuItems[] = [
                    'label' => 'Additional',
                    'url' => 'javascript:',
                    'icon' => 'list',
                    'items' => [
                        ['label' => 'Flight Segments', 'url' => ['lead-flight-segment/index'], 'icon' => 'plane'],
                        ['label' => 'Quote List', 'url' => ['quotes/index'], 'icon' => 'quora'],
                        ['label' => 'Quote Price List', 'url' => ['quote-price/index'], 'icon' => 'dollar'],

                        ['label' => 'Export Leads', 'url' => ['leads/export'], 'icon' => 'export'],
                        ['label' => 'Duplicate Leads', 'url' => ['leads/duplicate'], 'icon' => 'copy'],
                        ['label' => 'Stats Agents & Leads', 'url' => ['report/agents'], 'icon' => 'users'],
                        ['label' => 'Lead Status History', 'url' => ['lead-flow/index'], 'icon' => 'list'],

                    ]
                    //'linkOptions' => ['data-method' => 'post']
                ];
            }



            $menuItems[] = [
                'label' => 'Reports',
                'url' => 'javascript:',
                'icon' => 'bar-chart',
                'items' => [
                    ['label' => 'Sold', 'url' => ['report/sold']],
                ]
                //'linkOptions' => ['data-method' => 'post']
            ];





            if (!$isCoach) {
                $badges = \common\models\Lead::getBadgesSingleQuery();

                $menuItems[] = ['label' => 'Inbox <span id="inbox-queue" class="label-info label pull-right">' . $badges['inbox'] . '</span> ', 'url' => ['queue/inbox'], 'icon' => 'briefcase'];
                $menuItems[] = ['label' => 'Follow Up <span id="follow-up-queue" class="label-success label pull-right">' . $badges['follow-up'] . '</span> ', 'url' => ['queue/follow-up'], 'icon' => 'recycle'];
                $menuItems[] = ['label' => 'Processing <span id="processing-queue" class="label-warning label pull-right">' . $badges['processing'] . '</span> ', 'url' => ['queue/processing'], 'icon' => 'spinner'];

                if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
                    $menuItems[] = ['label' => 'Processing All <span id="processing-all-queue" class="label-warning label pull-right">' . $badges['processing-all'] . '</span> ', 'url' => ['queue/processing-all'], 'icon' => 'list'];
                }

                $menuItems[] = ['label' => 'Booked <span id="booked-queue" class="label-success label pull-right">' . $badges['booked'] . '</span> ', 'url' => ['queue/booked'], 'icon' => 'flag-o'];
                $menuItems[] = ['label' => 'Sold <span id="sold-queue" class="label-success label pull-right">' . $badges['sold'] . '</span> ', 'url' => ['queue/sold'], 'icon' => 'flag'];

                if($isAdmin || $isSupervision) {
                    $menuItems[] = ['label' => 'Trash <span id="trash-queue" class="label-danger label pull-right">' . $badges['trash'] . '</span> ', 'url' => ['queue/trash'], 'icon' => 'trash-o'];
                }
            }


            if($isSupervision) {
                $menuItems[] = ['label' => 'Users', 'url' => ['employee/list'], 'icon' => 'user'];
            }

            if($isAdmin) {
                $menuItems[] = [
                    'label' => 'Users',
                    'url' => 'javascript:',
                    'icon' => 'user',
                    'items' => [
                        ['label' => 'Users', 'url' => ['employee/list'], 'icon' => 'user'],
                        ['label' => 'User Groups', 'url' => ['user-group/index'], 'icon' => 'users'],
                        ['label' => 'User Groups Assignments', 'url' => ['user-group-assign/index'], 'icon' => 'users'],
                        ['label' => 'User Params', 'url' => ['user-params/index'], 'icon' => 'users']
                    ]
                    //'linkOptions' => ['data-method' => 'post']
                ];
                $menuItems[] = ['label' => 'Clients', 'url' => ['/client/index'], 'icon' => 'users'];

                $menuItems[] = [
                    'label' => 'Data Settings',
                    'url' => 'javascript:',
                    'icon' => 'list',
                    'items' =>  [
                        ['label' => 'Projects', 'url' => ['/settings/projects'], 'icon' => 'product-hunt'],
                        ['label' => 'Airlines', 'url' => ['/settings/airlines'], 'icon' => 'plane'],
                        ['label' => 'Airports', 'url' => ['/settings/airports'], 'icon' => 'plane'],
                        ['label' => 'ACL', 'url' => ['/settings/acl'], 'icon' => 'user-secret'],
                        ['label' => 'API Users', 'url' => ['/api-user/index'], 'icon' => 'users'],
                        ['label' => 'Tasks', 'url' => ['task/index'], 'icon' => 'list'],
                        ['label' => 'Lead Tasks', 'url' => ['lead-task/index'], 'icon' => 'list'],
                    ]
                ];

            }




            /*


                $menuItems[] = [
                    'label' => Yii::t('menu', 'Languages'),
                    'url' => 'javascript:',
                    'icon' => 'language',
                    'items' =>  [

                        ['label' => Yii::t('language', 'Language'), 'url' => 'javascript:',
                            'items' => [
                                ['label' => Yii::t('language', 'List of languages'), 'url' => ['/translatemanager/language/list']],
                                ['label' => Yii::t('language', 'Create'), 'url' => ['/translatemanager/language/create']],
                            ]
                        ],


                        ['label' => Yii::t('language', 'Scan'), 'url' => ['/translatemanager/language/scan']],
                        ['label' => Yii::t('language', 'Optimize'), 'url' => ['/translatemanager/language/optimizer']],
                        ['label' => Yii::t('language', 'Im-/Export'), 'url' => 'javascript:',
                            'items' => [
                                ['label' => Yii::t('language', 'Import'), 'url' => ['/translatemanager/language/import']],
                                ['label' => Yii::t('language', 'Export'), 'url' => ['/translatemanager/language/export']],
                            ]
                        ],
                    ]
                ];


                $menuItems[] = [
                    'label' => 'Users',
                    'url' => 'javascript:',
                    'icon' => 'users',
                    'items' => [
                        ['label' => UserManagementModule::t('back', 'Users'), 'url' => ['/user-management/user/index']],
                        ['label' => UserManagementModule::t('back', 'Roles'), 'url' => ['/user-management/role/index']],
                        ['label' => UserManagementModule::t('back', 'Permissions'), 'url' => ['/user-management/permission/index']],
                        ['label' => UserManagementModule::t('back', 'Permission groups'), 'url' => ['/user-management/auth-item-group/index']],
                        ['label' => UserManagementModule::t('back', 'Visit log'), 'url' => ['/user-management/user-visit-log/index']],
                    ]
                ];*/



            //$menuItems[] = ['label' => 'API Users', 'url' => ['/api-user/index'], 'icon' => 'users'];

            /*$menuItems[] = ['label' => 'Reports', 'url' => ['report/sold'], 'icon' => 'bar-chart'];

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
            ];*/



            if($isAdmin) {


                $menuItems[] = [
                    'label' => 'Logs & Tools',
                    'url' => 'javascript:',
                    'icon' => 'cog',
                    'items' => [
                        ['label' => 'API Logs', 'url' => ['/api-log/index'], 'icon' => 'sitemap'],
                        ['label' => 'System Logs', 'url' => ['/log/index'], 'icon' => 'bars'],
                        ['label' => 'Clear cache', 'url' => ['/tools/clear-cache'], 'icon' => 'remove'],
                    ]
                    //'linkOptions' => ['data-method' => 'post']
                ];
            }


            //if(\webvimark\modules\UserManagement\models\User::canRoute('/stats/index', $superAdminAllowed = true)) {
            //$menuItems[] = ['label' => \backend\widgets\ыуддштSysinfo::widget(['refresh' => 10]), 'url' => ['/stats/index']];
            //}


            //$menuItems[] = ['label' => \backend\widgets\Sysinfo::widget(['refresh' => 10]), 'url' => ['/stats/index'], 'icon' => ''];
        }


        //echo frontend\themes\gentelella\widgets\Menu::widget(['items' => $menuItems, 'encodeLabels' => false, 'activateParents' => true]);

        echo \yiister\gentelella\widgets\Menu::widget(['items' => $menuItems, 'encodeLabels' => false, 'activateParents' => true]);


        ?>



    </div>

</div>


