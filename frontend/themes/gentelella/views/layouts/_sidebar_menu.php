<?php
/* @var $this \yii\web\View */



$isAdmin = Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id);
$isSupervision = Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id);
$isAgent = Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id);
$isCoach = Yii::$app->authManager->getAssignment('coach', Yii::$app->user->id);
$isQA = Yii::$app->authManager->getAssignment('qa', Yii::$app->user->id);

?>
<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

    <div class="menu_section">


        <br>
        <br>
        <br>
        <?php /*<div class="text-center">
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



        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
        } else {

            /** @var \common\models\Employee $userModel */
            $userModel = Yii::$app->user->identity;

            if(!$isQA) {
                $menuItems[] = ['label' => 'Create new Lead', 'url' => ['lead/create'], 'icon' => 'plus'];
                //if($isAdmin) {
                if($userModel->userProfile && $userModel->userProfile->up_call_type_id != \common\models\UserProfile::CALL_TYPE_OFF) {
                    $menuItems[] = ['label' => 'Auto redial', 'url' => ['/call/auto-redial'], 'icon' => 'tty'];
                }
                //}
            }

            $menuItems[] = ['label' => 'Dashboard', 'url' => ['/'], 'icon' => 'area-chart'];


            if(!$isQA) {
                $menuItems[] = ['label' => 'Search Leads', 'url' => ['/leads/index'], 'icon' => 'search'];
            }


            //if($isAdmin) {
                $cntNotifications = \common\models\Notifications::findNewCount(Yii::$app->user->id);

                $menuItems[] = [
                    'label' => 'My Notifications' .
                        '<span id="div-cnt-notification">' . ($cntNotifications ? '<span class="label-success label pull-right">' . $cntNotifications . '</span>' : '') . '</span>',
                    'url' => ['/notifications/list'],
                    'icon' => 'comment',
                ];

                if(!$isQA) {
                    $menuItems[] = ['label' => 'My Mails <span id="email-inbox-queue" class="label-info label pull-right"></span> ', 'url' => ['/email/inbox'], 'icon' => 'envelope'];
                }

            //}



            //$sipExist = ($userModel->userProfile->up_sip && strlen($userModel->userProfile->up_sip) > 2);
            // //\common\models\UserProjectParams::find()->where(['upp_user_id' => Yii::$app->user->id])->andWhere(['AND', ['IS NOT', 'upp_tw_sip_id', null], ['<>', 'upp_tw_sip_id', '']])->exists();

            $smsExist = \common\models\UserProjectParams::find()->where(['upp_user_id' => Yii::$app->user->id])->andWhere(['AND', ['IS NOT', 'upp_tw_phone_number', null], ['<>', 'upp_tw_phone_number', '']])->exists();

            if($smsExist) {
                $menuItems[] = ['label' => 'My SMS <span id="sms-inbox-queue" class="label-info label pull-right"></span> ', 'url' => ['/sms/list'], 'icon' => 'comments'];
            }

            if($userModel->userProfile && $userModel->userProfile->up_call_type_id != \common\models\UserProfile::CALL_TYPE_OFF) {
                $menuItems[] = ['label' => 'My Calls <span id="call-inbox-queue" class="label-info label pull-right"></span> ', 'url' => ['/call/list'], 'icon' => 'phone'];
            }

            if($isAdmin || $isQA || $isSupervision) {

                if($isAdmin) {
                    $items =  [
                        ['label' => 'Agents report', 'url' => ['/agent-report'], 'icon' => 'users'],
                        ['label' => 'Calls & SMS', 'url' => ['/stats/call-sms'], 'icon' => 'phone'],
                        ['label' => 'Stats Employees', 'url' => ['/stats/index'], 'icon' => 'users'],
                        ['label' => 'User Connections', 'url' => ['/user-connection/index'], 'icon' => 'plug'],
                        ['label' => 'User Stats', 'url' => ['/user-connection/stats'], 'icon' => 'area-chart'],
                        ['label' => 'Call User Map', 'url' => ['/call/user-map'], 'icon' => 'map'],
                    ];
                }

                if($isQA || $isSupervision) {
                    $items =  [
                        ['label' => 'Calls & SMS', 'url' => ['/stats/call-sms'], 'icon' => 'list'],

                        ['label' => 'Call List', 'url' => ['/call/index'], 'icon' => 'phone'],
                        ['label' => 'SMS List', 'url' => ['/sms/index'], 'icon' => 'comments-o'],
                        ['label' => 'Mail List', 'url' => ['/email/index'], 'icon' => 'envelope'],
                    ];
                }

                $menuItems[] = [
                    'label' => 'Stats',
                    'url'   => 'javascript:',
                    'icon'  => 'bar-chart',
                    'items' =>  $items,
                ];
            }



            if($isAdmin || $isSupervision) {

                $items = [];
                if($isAdmin) {
                    $items =  [

                        ['label' => 'All Notifications', 'url' => ['/notifications/index'], 'icon' => 'comment-o'],
                        ['label' => 'Call List', 'url' => ['/call/index'], 'icon' => 'phone'],
                        ['label' => 'SMS List', 'url' => ['/sms/index'], 'icon' => 'comments-o'],
                        ['label' => 'Mail List', 'url' => ['/email/index'], 'icon' => 'envelope'],
                        ['label' => 'User Call Statuses', 'url' => ['/user-call-status/index'], 'icon' => 'list'],

                    ];
                }

                $items = array_merge($items, [
                    ['label' => 'Flight Segments', 'url' => ['lead-flight-segment/index'], 'icon' => 'plane'],
                    ['label' => 'Quote List', 'url' => ['quotes/index'], 'icon' => 'quora'],
                    ['label' => 'Quote Price List', 'url' => ['quote-price/index'], 'icon' => 'dollar'],

                    ['label' => 'Export Leads', 'url' => ['leads/export'], 'icon' => 'export'],
                    ['label' => 'Duplicate Leads', 'url' => ['leads/duplicate'], 'icon' => 'copy'],
                    ['label' => 'Stats Agents & Leads', 'url' => ['report/agents'], 'icon' => 'users'],
                    ['label' => 'Lead Status History', 'url' => ['lead-flow/index'], 'icon' => 'list'],
                ]);

                $menuItems[] = [
                    'label' => 'Additional',
                    'url' => 'javascript:',
                    'icon' => 'list',
                    'items' => $items
                    //'linkOptions' => ['data-method' => 'post']
                ];
            }



            if($isAdmin || ($isAgent && $userModel->userProfile && $userModel->userProfile->up_kpi_enable))
            {
                $menuItems[] = ['label' => 'KPI <span id="kpi" class="label-info label pull-right"></span> ', 'url' => ['/kpi/index'], 'icon' => 'money'];
            }


            if($isQA) {
                $menuItems[] = ['label' => 'Sold', 'url' => ['queue/sold'], 'icon' => 'flag text-success'];
                $menuItems[] = ['label' => 'Duplicate', 'url' => ['queue/duplicate'], 'icon' => 'list text-danger'];
                $menuItems[] = ['label' => 'Trash', 'url' => ['queue/trash'], 'icon' => 'trash-o text-danger'];
            }


            if (!$isCoach && !$isQA) {

                $badges = \common\models\Lead::getBadgesSingleQuery();

                if($isAdmin) {
                    $menuItems[] = ['label' => 'Pending <span id="pending-queue" class="label-info label pull-right">' . $badges['pending'] . '</span> ', 'url' => ['queue/pending'], 'icon' => 'briefcase text-info'];
                }

                if(isset(Yii::$app->params['settings']['enable_lead_inbox']) && Yii::$app->params['settings']['enable_lead_inbox']) {
                    $menuItems[] = ['label' => 'Inbox <span id="inbox-queue" class="label-info label pull-right">' . $badges['inbox'] . '</span> ', 'url' => ['queue/inbox'], 'icon' => 'briefcase text-info'];
                }
                $menuItems[] = ['label' => 'Follow Up <span id="follow-up-queue" class="label-success label pull-right">' . $badges['follow-up'] . '</span> ', 'url' => ['queue/follow-up'], 'icon' => 'recycle'];
                $menuItems[] = ['label' => 'Processing <span id="processing-queue" class="label-warning label pull-right">' . $badges['processing'] . '</span> ', 'url' => ['queue/processing'], 'icon' => 'spinner'];

                /*if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
                    $menuItems[] = ['label' => 'Processing All <span id="processing-all-queue" class="label-warning label pull-right">' . $badges['processing-all'] . '</span> ', 'url' => ['queue/processing-all'], 'icon' => 'list'];
                }*/

                $menuItems[] = ['label' => 'Booked <span id="booked-queue" class="label-success label pull-right">' . $badges['booked'] . '</span>', 'url' => ['queue/booked'], 'icon' => 'flag-o text-warning'];
                $menuItems[] = ['label' => 'Sold <span id="sold-queue" class="label-success label pull-right">' . $badges['sold'] . '</span> ', 'url' => ['queue/sold'], 'icon' => 'flag text-success'];

                if($isAdmin || $isSupervision) {
                    $menuItems[] = ['label' => 'Duplicate <span id="sold-queue" class="label-danger label pull-right">' . $badges['duplicate'] . '</span>', 'url' => ['queue/duplicate'], 'icon' => 'list text-danger'];
                    $menuItems[] = ['label' => 'Trash <span id="trash-queue" class="label-danger label pull-right"></span>', 'url' => ['queue/trash'], 'icon' => 'trash-o text-danger']; //' . $badges['trash'] . '
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
                        ['label' => 'User Params', 'url' => ['user-params/index'], 'icon' => 'users'],
                        ['label' => 'User Project Params', 'url' => ['user-project-params/index'], 'icon' => 'users']
                    ]
                    //'linkOptions' => ['data-method' => 'post']
                ];

                //$menuItems[] = ['label' => 'Clients', 'url' => ['/client/index'], 'icon' => 'users'];
                $menuItems[] = [
                    'label' => 'Clients',
                    'url' => 'javascript:',
                    'icon' => 'users',
                    'items' =>  [

                        ['label' => 'Clients', 'url' => ['/client/index'], 'icon' => 'users'],
                        ['label' => 'Clients phones', 'url' => ['/client-phone'], 'icon' => 'phone'],
                    ]
                ];


                $menuItems[] = [
                    'label' => 'Data Settings',
                    'url' => 'javascript:',
                    'icon' => 'list',
                    'items' =>  [

                        ['label' => 'Projects', 'url' => ['/project/index'], 'icon' => 'product-hunt'],
                        ['label' => 'Project Sources', 'url' => ['/sources/index'], 'icon' => 'product-hunt'],
                        ['label' => 'Airlines', 'url' => ['/settings/airlines'], 'icon' => 'plane'],
                        ['label' => 'Airports', 'url' => ['/settings/airports'], 'icon' => 'plane'],
                        ['label' => 'ACL', 'url' => ['/settings/acl'], 'icon' => 'user-secret'],
                        ['label' => 'API Users', 'url' => ['/api-user/index'], 'icon' => 'users'],
                        ['label' => 'Tasks', 'url' => ['task/index'], 'icon' => 'list'],
                        ['label' => 'Lead Tasks', 'url' => ['lead-task/index'], 'icon' => 'list'],
                        ['label' => 'Email template types', 'url' => ['/email-template-type/index'], 'icon' => 'envelope-o'],
                        ['label' => 'SMS template types', 'url' => ['/sms-template-type/index'], 'icon' => 'comments-o'],

                        ['label' => 'Project Settings', 'url' => ['/settings/projects'], 'icon' => 'product-hunt'],

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
                    'label' => 'Logs & Tools',
                    'url' => 'javascript:',
                    'icon' => 'cog',
                    'items' => [
                        ['label' => 'API Logs', 'url' => ['/api-log/index'], 'icon' => 'sitemap'],
                        ['label' => 'System Logs', 'url' => ['/log/index'], 'icon' => 'bars'],
                        //['label' => 'Clear cache', 'url' => ['/tools/clear-cache'], 'icon' => 'remove'],
                        ['label' => 'Clean cache & assets', 'url' => ['/clean/index'], 'icon' => 'remove'],
                        //['label' => 'Supervisor Service', 'url' => ['/tools/supervisor'], 'icon' => 'user'],
                        ['label' => 'Site Settings', 'url' => ['/setting/index'], 'icon' => 'cogs'],
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


