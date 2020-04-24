<?php

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use \yii\helpers\Url;

/* @var $this \yii\web\View */
/** @var \common\models\Employee $user */

$user = Yii::$app->user->identity;

$isAdmin = $user->isAdmin() || $user->isSuperAdmin();
$isSupervision = $user->isSupervision();
$isAgent = $user->isAgent();
$isQA = $user->isQa();
$isUM = $user->isUserManager();
$isSuperAdmin = $user->isSuperAdmin();

?>
<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
<!--        <br>-->
<!--        <br>-->
<!--        <br>-->
        <?php

        $menuItems = [];




//        if ($user->canCall()) {
//            $menuItems[] = ['label' => 'Auto redial', 'url' => ['/call/auto-redial'], 'icon' => 'tty'];
//        }

        $menuItems[] = ['label' => 'Dashboard', 'url' => ['/dashboard/index'], 'icon' => 'area-chart'];

        $menuLItems = [];

        $menuLItems[] = ['label' => 'Create Lead', 'url' => ['/lead/create'], 'icon' => 'plus'];
        $menuLItems[] = ['label' => 'Create New Lead', 'url' => ['/lead/create2'], 'icon' => 'plus', 'attributes' => ['data-ajax-link' => true, 'data-modal-title' => 'Create New Lead']];


        $menuLItems[] = ['label' => 'Search Leads', 'url' => ['/leads/index'], 'icon' => 'search'];

        if (($profile = $user->userProfile) && $profile->up_auto_redial) {
            $menuLItems[] = ['label' => 'Lead Redial <span id="badges-redial" data-type="redial" class="label-info label pull-right bginfo"></span>', 'url' => ['/lead-redial/index'], 'icon' => 'phone'];
        }

        $menuLItems[] = ['label' => 'New', 'url' => ['/lead/new'], 'icon' => 'paste text-warning'];

        $menuLItems[] = ['label' => 'Pending <span id="badges-pending" data-type="pending" class="label-info label pull-right bginfo"></span> ', 'url' => ['/queue/pending'], 'icon' => 'briefcase'];

        if (isset(Yii::$app->params['settings']['enable_lead_inbox']) && Yii::$app->params['settings']['enable_lead_inbox']) {
            $menuLItems[] = ['label' => 'Inbox <span id="badges-inbox" data-type="inbox" class="label-info label pull-right bginfo"></span> ', 'url' => ['/queue/inbox'], 'icon' => 'briefcase'];
        }



        $menuLItems[] = ['label' => 'Bonus <span id="badges-bonus" data-type="bonus" class="label-success label pull-right bginfo"></span> ', 'url' => ['/queue/bonus'], 'icon' => 'recycle'];
        $menuLItems[] = ['label' => 'Follow Up <span id="badges-follow-up" data-type="follow-up" class="label-success label pull-right bginfo"></span> ', 'url' => ['/queue/follow-up'], 'icon' => 'recycle'];
        $menuLItems[] = ['label' => 'Processing <span id="badges-processing" data-type="processing" class="label-warning label pull-right bginfo"></span> ', 'url' => ['/queue/processing'], 'icon' => 'spinner'];
        $menuLItems[] = ['label' => 'Booked <span id="badges-booked" data-type="booked" class="label-success label pull-right bginfo"></span>', 'url' => ['/queue/booked'], 'icon' => 'flag-o text-warning'];
        $menuLItems[] = ['label' => 'Sold <span id="badges-sold" data-type="sold" class="label-success label pull-right bginfo"></span> ', 'url' => ['/queue/sold'], 'icon' => 'flag text-success'];
        $menuLItems[] = ['label' => 'Duplicate <span id="badges-duplicate" data-type="duplicate" class="label-danger label pull-right bginfo"></span>', 'url' => ['/queue/duplicate'], 'icon' => 'list text-danger'];
        $menuLItems[] = ['label' => 'Trash <span id="badges-trash" class="label-danger label pull-right"></span>', 'url' => ['/queue/trash'], 'icon' => 'trash-o text-danger'];

        $menuLItems[] = ['label' => 'Import Leads', 'url' => ['/lead/import'], 'icon' => 'upload'];


        if($isAdmin) {
            $menuItems[] = [
                'label' => 'Leads',
                'url' => 'javascript:',
                'icon' => 'cubes',
                'items' => $menuLItems
            ];
        } else {
            $menuItems = \yii\helpers\ArrayHelper::merge($menuItems, $menuLItems);
        }

        $menuCases = [];
        $menuCases[] = ['label' => 'Create new Case', 'url' => ['/cases/create'], 'icon' => 'plus'];
        $menuCases[] = ['label' => 'Search Cases', 'url' => ['/cases/index'], 'icon' => 'search'];

        $menuCases[] = ['label' => 'Case Need Action <span id="cases-q-need-action" data-type="need-action" class="label-warning label pull-right cases-q-info"></span> ', 'url' => ['/cases-q/need-action'], 'icon' => 'briefcase text-info'];
        $menuCases[] = ['label' => 'Case Pending <span id="cases-q-pending" data-type="pending" class="label-warning label pull-right cases-q-info"></span> ', 'url' => ['/cases-q/pending'], 'icon' => 'briefcase text-info'];
        $menuCases[] = ['label' => 'Case Inbox <span id="cases-q-inbox" data-type="inbox" class="label-warning label pull-right cases-q-info"></span> ', 'url' => ['/cases-q/inbox'], 'icon' => 'briefcase text-info'];
        $menuCases[] = ['label' => 'Case Processing <span id="cases-q-processing" data-type="processing" class="label-warning label pull-right cases-q-info"></span> ', 'url' => ['/cases-q/processing'], 'icon' => 'spinner'];
        $menuCases[] = ['label' => 'Case Follow Up <span id="cases-q-follow-up" data-type="follow-up" class="label-success label pull-right cases-q-info"></span> ', 'url' => ['/cases-q/follow-up'], 'icon' => 'recycle'];
        $menuCases[] = ['label' => 'Case Solved <span id="cases-q-solved" data-type="solved" class="label-success label pull-right cases-q-info"></span> ', 'url' => ['/cases-q/solved'], 'icon' => 'flag text-success'];
        $menuCases[] = ['label' => 'Case Trash <span id="cases-q-trash" class="label-danger label pull-right"></span>', 'url' => ['/cases-q/trash'], 'icon' => 'trash-o text-danger'];

        if ($isAdmin) {
            $menuItems[] = [
                'label' => 'Cases',
                'url' => 'javascript:',
                'icon' => 'cubes',
                'items' => $menuCases
            ];
        } else {
            $menuItems = \yii\helpers\ArrayHelper::merge($menuItems, $menuCases);
        }



        $menuItems[] = ['label' => 'Search Sale', 'url' => ['/sale/search'], 'icon' => 'search'];
        //$menuItems[] = ['label' => 'Search Cases', 'url' => ['/cases/index'], 'icon' => 'search'];


        if ($isAdmin || $user->isKpiEnable()) {
            $menuItems[] = ['label' => 'KPI <span id="kpi" class="label-info label pull-right"></span> ', 'url' => ['/kpi/index'], 'icon' => 'money'];
        }

        if (!$isUM) {
           // $cntNotifications = \common\models\Notifications::findNewCount(Yii::$app->user->id);
            $cntNotifications = null;
            $menuItems[] = [
                'label' => 'My Notifications' .
//                    '<span id="div-cnt-notification">' . ($cntNotifications ? '<span class="label-success label pull-right">' . $cntNotifications . '</span>' : '') . '</span>',
                    '<span id="div-cnt-notification"><span class="label-success label pull-right notification-counter"></span></span>',
                'url' => ['/notifications/list'],
                'icon' => 'comment',
            ];
        }

        $menuItems[] = ['label' => 'My Mails <span id="email-inbox-queue" class="label-info label pull-right"></span> ', 'url' => ['/email/inbox'], 'icon' => 'envelope'];

//        $smsExist = \common\models\UserProjectParams::find()
//            ->where(['upp_user_id' => Yii::$app->user->id])
//            ->andWhere([
//                'AND', ['IS NOT', 'upp_tw_phone_number', null],
//                ['<>', 'upp_tw_phone_number', '']
//            ])
//            ->exists();

        $smsExist = \common\models\UserProjectParams::find()
            ->andWhere(['upp_user_id' => Yii::$app->user->id])
            ->innerJoinWith('phoneList', false)
            ->exists();

        if ($smsExist) {
            $menuItems[] = ['label' => 'My SMS <span id="sms-inbox-queue" class="label-info label pull-right"></span> ', 'url' => ['/sms/list'], 'icon' => 'comments'];
        }

        if ($user->canCall()) {
            $menuItems[] = ['label' => 'My Calls <span id="call-inbox-queue" class="label-info label pull-right"></span> ', 'url' => ['/call/list'], 'icon' => 'phone'];
        }

        $menuItems[] = [
            'label' => 'Data Lists',
            'url' => 'javascript:',
            'icon' => 'th-list',
            'items' => [
                ['label' => 'Call List', 'url' => ['/call/index'], 'icon' => 'phone'],
                [
                    'label' => 'Call logs',
                    'url' => 'javascript:',
                    'icon' => 'phone',
                    'items' => [
                        ['label' => 'Log', 'url' => ['/call-log/index'], 'icon' => 'list'],
                        ['label' => 'Cases', 'url' => ['/call-log-case/index'], 'icon' => 'list'],
                        ['label' => 'Leads', 'url' => ['/call-log-lead/index'], 'icon' => 'list'],
                        ['label' => 'Queue', 'url' => ['/call-log-queue/index'], 'icon' => 'list'],
                        ['label' => 'Record', 'url' => ['/call-log-record/index'], 'icon' => 'list'],
                    ],
                ],
                ['label' => 'SMS List', 'url' => ['/sms/index'], 'icon' => 'comments-o'],
                ['label' => 'SMS Distrib List', 'url' => ['/sms-distribution-list/index'], 'icon' => 'comments warning'],
                ['label' => 'Mail List', 'url' => ['/email/index'], 'icon' => 'envelope'],
                ['label' => 'Notification List', 'url' => ['/notifications/index'], 'icon' => 'comment-o'],
                ['label' => 'Conference Room', 'url' => ['/conference-room/index'], 'icon' => 'comment'],
                ['label' => 'Conferences', 'url' => ['/conference/index'], 'icon' => 'comment'],
                ['label' => 'Conference Participant', 'url' => ['/conference-participant/index'], 'icon' => 'phone'],
            ]
        ];



        $menuQCall = [
            'label' => 'QCall',
            'url' => 'javascript:',
            'icon' => 'phone',
            'items' => [
                ['label' => 'Lead QCall List', 'url' => ['/lead-qcall/list'], 'icon' => 'list'],
                ['label' => 'Lead QCall All', 'url' => ['/lead-qcall/index'], 'icon' => 'list'],
                ['label' => 'QCall Config', 'url' => ['/qcall-config/index'], 'icon' => 'list'],
                ['label' => 'Project Weight', 'url' => ['/project-weight/index'], 'icon' => 'list'],
                ['label' => 'Status Weight', 'url' => ['/status-weight/index'], 'icon' => 'list'],
            ]
        ];

        $menuItems[] = [
            'label' => 'Additional',
            'url' => 'javascript:',
            'icon' => 'list',
            'items' => [
                $menuQCall,
                ['label' => 'User Call Statuses', 'url' => ['/user-call-status/index'], 'icon' => 'list'],
                ['label' => 'Lead Call Experts', 'url' => ['/lead-call-expert/index'], 'icon' => 'bell'],
                ['label' => 'Flight Segments', 'url' => ['/lead-flight-segment/index'], 'icon' => 'plane'],
                ['label' => 'Quote List', 'url' => ['/quotes/index'], 'icon' => 'quora', 'iconPrefix' => 'fab'],
                ['label' => 'Quote Price List', 'url' => ['/quote-price/index'], 'icon' => 'dollar'],
                ['label' => 'Export Leads', 'url' => ['/leads/export'], 'icon' => 'export'],
                ['label' => 'Duplicate Leads', 'url' => ['/leads/duplicate'], 'icon' => 'copy'],
                ['label' => 'Stats Agents & Leads', 'url' => ['/report/agents'], 'icon' => 'users'],
                ['label' => 'Lead Status History', 'url' => ['/lead-flow/index'], 'icon' => 'list'],
                ['label' => 'Lead Check Lists', 'url' => ['/lead-checklist/index'], 'icon' => 'list', 'visible' => Yii::$app->user->can('manageLeadChecklist')],
                ['label' => 'LF Checklist Status History', 'url' => ['/lead-flow-checklist/index'], 'icon' => 'list', 'visible' => Yii::$app->user->can('viewLeadFlowChecklist')],
                ['label' => 'Call User Access', 'url' => ['/call-user-access/index'], 'icon' => 'list'],
                ['label' => 'Phone Blacklist', 'url' => ['/phone-blacklist/index'], 'icon' => 'phone'],

            ]
        ];

        $menuItems[] = [
            'label' => 'Users',
            'url' => 'javascript:',
            'icon' => 'user',
            'items' => [
                ['label' => 'Users', 'url' => ['/employee/list'], 'icon' => 'user'],
                ['label' => 'User Groups', 'url' => ['/user-group/index'], 'icon' => 'users'],
                ['label' => 'User Groups Set', 'url' => ['/user-group-set/index'], 'icon' => 'users'],
                ['label' => 'User Params', 'url' => ['/user-params/index'], 'icon' => 'bars'],
                ['label' => 'User Project Params', 'url' => ['/user-project-params/index'], 'icon' => 'list'],
                ['label' => 'User Groups Assignments', 'url' => ['/user-group-assign/index'], 'icon' => 'list'],
                [
                    'label' => 'User Product Type',
                    'url' => ['/user-product-type/index'],
                    'icon' => 'list',
                    'visible' => Yii::$app->user->can('user-product-type/list')
                ],
                ['label' => 'User Status', 'url' => ['/user-status/index'], 'icon' => 'sliders'],
                ['label' => 'User Online', 'url' => ['/user-online/index'], 'icon' => 'plug'],
                ['label' => 'User Connections', 'url' => ['/user-connection/index'], 'icon' => 'plug'],
                ['label' => 'Visitor Log', 'url' => ['/visitor-log/index'], 'icon' => 'list'],
                ['label' => 'User Commission Rules', 'url' => ['/user-commission-rules-crud/index'], 'icon' => 'list'],
                ['label' => 'User Bonus Rules', 'url' => ['/user-bonus-rules-crud/index'], 'icon' => 'list'],
            ]
        ];

        $menuItems[] = [
            'label' => 'Clients',
            'url' => 'javascript:',
            'icon' => 'users',
            'items' => [
                ['label' => 'Clients', 'url' => ['/client/index'], 'icon' => 'users'],
                ['label' => 'Clients phones', 'url' => ['/client-phone/index'], 'icon' => 'phone'],
                ['label' => 'Clients emails', 'url' => ['/client-email/index'], 'icon' => 'envelope '],
                ['label' => 'Contacts', 'url' => ['/contacts/index'], 'icon' => 'user'],
                ['label' => 'User Contact Lists', 'url' => ['/user-contact-list/index'], 'icon' => 'sitemap'],
                ['label' => 'Client Project', 'url' => ['/client-project/index'], 'icon' => 'bars'],
            ]
        ];

        $menuNewData = [
            'label' => 'New Data',
            'url' => 'javascript:',
            'icon' => 'list',
            'items' => [
                ['label' => 'Currency List', 'url' => ['/currency/index']],
                ['label' => 'Currency History', 'url' => ['/currency-history/index']],
                ['label' => 'Product Types', 'url' => ['/product/product-type-crud/index']],
                ['label' => 'Product Type Payment Method', 'url' => ['/product/product-type-payment-method/index']],
                ['label' => 'Products', 'url' => ['/product/product-crud/index']],
                ['label' => 'Product Options', 'url' => ['/product/product-option-crud/index']],
                ['label' => 'Product Quotes', 'url' => ['/product/product-quote-crud/index']],
                ['label' => 'Product Quotes Status Log', 'url' => ['/product/product-quote-status-log-crud/index']],
                ['label' => 'Product Quote Options', 'url' => ['/product/product-quote-option-crud/index']],
                ['label' => 'Orders', 'url' => 'javascript:', 'items' => [
                    ['label' => 'Orders', 'url' => ['/order/order-crud/index']],
                    ['label' => 'Orders Status Log', 'url' => ['/order/order-status-log-crud/index']],
                    ['label' => 'Orders User Profit', 'url' => ['/order/order-user-profit-crud/index']],
                    ['label' => 'Orders Tips', 'url' => ['/order/order-tips-crud/index']],
                    ['label' => 'Orders Tips User Profit', 'url' => ['/order/order-tips-user-profit-crud/index']],
                ], 'hasChild' => true],
                ['label' => 'Offers', 'url' => ['/offer/offer-crud/index']],
                ['label' => 'Offers Send Log', 'url' => ['/offer/offer-send-log-crud/index']],
                ['label' => 'Offers View Log', 'url' => ['/offer/offer-view-log-crud/index']],
                ['label' => 'Offers Status Log', 'url' => ['/offer/offer-status-log-crud/index']],
                ['label' => 'Offer Products', 'url' => ['/offer/offer-product-crud/index']],
                ['label' => 'Invoices', 'url' => ['/invoice/invoice-crud/index']],
                ['label' => 'Invoices Status Log', 'url' => ['/invoice/invoice-status-log-crud/index']],
                ['label' => 'Billing Info', 'url' => ['/billing-info/index']],
                ['label' => 'Credit Cards', 'url' => ['/credit-card/index']],
                ['label' => 'Payments', 'url' => ['/payment/index']],
                ['label' => 'Payment Methods', 'url' => ['/payment-method/index']],
                ['label' => 'Transactions', 'url' => ['/transaction/index']],
                ['label' => 'Payroll', 'url' => 'javascript:', 'items' => [
                    ['label' => 'User Payment', 'url' => '/user-payment-crud/index'],
                    ['label' => 'User Payment Category', 'url' => '/user-payment-category-crud/index'],
                    ['label' => 'User Payroll', 'url' => '/user-payroll-crud/index'],
                    ['label' => 'User Profit', 'url' => '/user-profit-crud/index'],
                ], 'hasChild' => true],

                ['label' => 'KPI', 'url' => 'javascript:', 'items' => [
                    ['label' => 'KPI User Performance', 'url' => '/kpi-user-performance-crud/index'],
                    ['label' => 'KPI Product Commission', 'url' => '/kpi-product-commission-crud/index'],
                    ['label' => 'KPI User Product Commission', 'url' => '/kpi-user-product-commission-crud/index'],
                ], 'hasChild' => true],
                ['label' => 'Lead Profit Type', 'url' => ['/lead-profit-type-crud/index']]
            ]
        ];

        $menuItems[] = [
            'label' => 'Data Settings',
            'url' => 'javascript:',
            'icon' => 'list',
            'items' => [
                    $menuNewData,
                ['label' => 'Projects', 'url' => ['/project/index'], 'icon' => 'product-hunt'],
                ['label' => 'Project Sources', 'url' => ['/sources/index'], 'icon' => 'product-hunt'],
                ['label' => 'Phone List', 'url' => ['/phone-list/index'], 'icon' => 'list'],
                ['label' => 'Email List', 'url' => ['/email-list/index'], 'icon' => 'list'],
                ['label' => 'Departments', 'url' => ['/department/index'], 'icon' => 'sitemap'],
                ['label' => 'Department Emails', 'url' => ['/department-email-project/index'], 'icon' => 'envelope'],
                ['label' => 'Department Phones', 'url' => ['/department-phone-project/index'], 'icon' => 'phone'],
                ['label' => 'Airlines', 'url' => ['/settings/airlines'], 'icon' => 'plane'],
                ['label' => 'Airports', 'url' => ['/settings/airports'], 'icon' => 'plane'],
                ['label' => 'ACL', 'url' => ['/settings/acl'], 'icon' => 'user-secret'],
                ['label' => 'API Users', 'url' => ['/api-user/index'], 'icon' => 'users'],
                ['label' => 'Tasks', 'url' => ['/task/index'], 'icon' => 'list'],
                ['label' => 'Lead Tasks', 'url' => ['/lead-task/index'], 'icon' => 'list'],
                ['label' => 'Email template types', 'url' => ['/email-template-type/index'], 'icon' => 'envelope-o'],
                ['label' => 'SMS template types', 'url' => ['/sms-template-type/index'], 'icon' => 'comments-o'],
                ['label' => 'Case Sales', 'url' => ['/case-sale/index'], 'icon' => 'list'],
                ['label' => 'Case Notes', 'url' => ['/case-note/index'], 'icon' => 'list'],
                ['label' => 'Project Settings', 'url' => ['/settings/projects'], 'icon' => 'product-hunt'],
                ['label' => 'Check List Types', 'url' => ['/lead-checklist-type/index'], 'icon' => 'list', 'visible' => Yii::$app->user->can('manageLeadChecklistType')],
                ['label' => 'Case status history', 'url' => ['/case-status-log/index'], 'icon' => 'bars'],
                ['label' => 'Case categories', 'url' => ['/case-category/index'], 'icon' => 'list'],
            ]
        ];


        $menuModuleItems = [];

        if (class_exists('\modules\flight\FlightModule')) {
            $menuModuleItems[] = [
                'label' => 'Flight module',
                'url' => 'javascript:',
                'icon' => 'plane',
                'items' => \modules\flight\FlightModule::getListMenu()
            ];
        }

        if (class_exists('\modules\hotel\HotelModule')) {
            $menuModuleItems[] = [
                'label' => 'Hotel module',
                'url' => 'javascript:',
                'icon' => 'hotel',
                'items' => \modules\hotel\HotelModule::getListMenu()
            ];
        }

        if ($menuModuleItems) {
            $menuItems[] = [
                'label' => 'Modules',
                'url' => 'javascript:',
                'icon' => 'windows',
                'items' => $menuModuleItems
            ];
        }

        $menuItems[] = [
            'label' => 'QA Tasks',
            'url' => 'javascript:',
            'icon' => 'check-square-o',
            'items' => [
                ['label' => 'Search', 'url' => ['/qa-task/qa-task-queue/search']],
                ['label' => 'Pending <span id="qa-task-q-pending" data-type="pending" class="badge badge-'.QaTaskStatus::getCssClass(QaTaskStatus::PENDING).' pull-right qa-task-info"></span>', 'url' => ['/qa-task/qa-task-queue/pending']],
                ['label' => 'Processing <span id="qa-task-q-processing" data-type="processing" class="badge badge-'.QaTaskStatus::getCssClass(QaTaskStatus::PROCESSING).' pull-right qa-task-info"></span>', 'url' => ['/qa-task/qa-task-queue/processing']],
                ['label' => 'Escalated <span id="qa-task-q-escalated" data-type="escalated" class="badge badge-'.QaTaskStatus::getCssClass(QaTaskStatus::ESCALATED).' pull-right qa-task-info"></span>', 'url' => ['/qa-task/qa-task-queue/escalated']],
                ['label' => 'Closed <span id="qa-task-q-closed" data-type="closed" class="badge badge-'.QaTaskStatus::getCssClass(QaTaskStatus::CLOSED).' pull-right qa-task-info"></span>', 'url' => ['/qa-task/qa-task-queue/closed']],
                ['label' => 'Tasks', 'url' => ['/qa-task/qa-task-crud/index']],
                ['label' => 'Categories', 'url' => ['/qa-task/qa-task-category-crud/index']],
                ['label' => 'Statuses', 'url' => ['/qa-task/qa-task-status-crud/index']],
                ['label' => 'Action Reasons', 'url' => ['/qa-task/qa-task-action-reason-crud/index']],
                ['label' => 'Status log', 'url' => ['/qa-task/qa-task-status-log-crud/index']],
                ['label' => 'Rules', 'url' => ['/qa-task/qa-task-rules/index']],
            ]
        ];


        $menuItems[] = [
            'label' => 'Stats & Reports',
            'url' => 'javascript:',
            'icon' => 'bar-chart',
            'items' => [
                ['label' => 'Agents report', 'url' => ['/agent-report/index'], 'icon' => 'users'],
                ['label' => 'Calls & SMS', 'url' => ['/stats/call-sms'], 'icon' => 'phone'],
                ['label' => 'Calls Report', 'url' => ['/report/calls-report'], 'icon' => 'table'],
                ['label' => 'Agent Calls Report', 'url' => ['/stats/calls-stats'], 'icon' => 'table'],
                ['label' => 'Leads Report', 'url' => ['/report/leads-report'], 'icon' => 'table'],
                ['label' => 'Agent Leads Report', 'url' => ['/stats/leads-stats'], 'icon' => 'table'],
                ['label' => 'Calls Stats', 'url' => ['/stats/calls-graph'], 'icon' => 'line-chart'],
                ['label' => 'SMS Stats', 'url' => ['/stats/sms-graph'], 'icon' => 'line-chart'],
                ['label' => 'Emails Stats', 'url' => ['/stats/emails-graph'], 'icon' => 'line-chart'],
                ['label' => 'Stats Employees', 'url' => ['/stats/index'], 'icon' => 'users'],

                ['label' => 'User Stats', 'url' => ['/user-connection/stats'], 'icon' => 'area-chart'],
                ['label' => 'Call User Map', 'url' => ['/call/user-map'], 'icon' => 'map'],
                ['label' => 'Agents Ratings', 'url' => ['/stats/agent-ratings'], 'icon' => 'star-half-empty'],
            ]
        ];


        $menuLanguages = [
            'label' => Yii::t('menu', 'Languages'),
            'url' => 'javascript:',
            'icon' => 'language',
            'items' => [
                [
                    'label' => Yii::t('language', 'Language'), 'url' => 'javascript:',
                    'items' => [
                        ['label' => Yii::t('language', 'List of languages'), 'url' => ['/translatemanager/language/list']],
                        ['label' => Yii::t('language', 'Create'), 'url' => ['/translatemanager/language/create']],
                    ]
                ],
                ['label' => Yii::t('language', 'Scan'), 'url' => ['/translatemanager/language/scan']],
                ['label' => Yii::t('language', 'Optimize'), 'url' => ['/translatemanager/language/optimizer']],
                [
                    'label' => Yii::t('language', 'Im-/Export'), 'url' => 'javascript:',
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
                ['label' => 'System Logs', 'url' => ['/log/index'], 'icon' => 'bug text-warning'],
                ['label' => 'API Logs', 'url' => ['/api-log/index'], 'icon' => 'sitemap'],
                ['label' => 'API Report', 'url' => ['/stats/api-graph'], 'icon' => 'bar-chart'],
                ['label' => 'Action Logs', 'url' => ['/log/action'], 'icon' => 'bars'],

                ['label' => 'User Site Activity', 'url' => ['/user-site-activity/index'], 'icon' => 'bars'],
                ['label' => 'User Activity Report', 'url' => ['/user-site-activity/report'], 'icon' => 'bar-chart'],
				['label' => 'Global Model Logs', 'url' => ['/global-log/index'], 'icon' => 'list'],
                ['label' => 'Clean cache & assets', 'url' => ['/clean/index'], 'icon' => 'remove'],
                [
                    'label' => Yii::t('language', 'Tools'), 'url' => 'javascript:', 'icon' => 'cog',
                    'items' => [
                        ['label' => Yii::t('language', 'Check Flight Dump'), 'url' => ['/tools/check-flight-dump']],
                    ]
                ],

                $menuLanguages,

                ['label' => 'Site Settings Category', 'url' => ['/setting-category/index'], 'icon' => 'list'],
                ['label' => 'Site Settings', 'url' => ['/setting/index'], 'icon' => 'cogs'],
            ]
        ];

        $menuItems[] = [
            'label' => 'RBAC',
            'url' => 'javascript:',
            'icon' => 'cogs',
            'items' => [
                ['label' => 'Assignment', 'url' => ['/rbac/assignment']],
                ['label' => 'Route', 'url' => ['/rbac/route']],
                ['label' => 'Permission', 'url' => ['/rbac/permission']],
                ['label' => 'Role', 'url' => ['/rbac/role']],
                ['label' => 'Import / Export', 'url' => ['/rbac-import-export/log']],
            ],
        ];

        ensureVisibility($menuItems);

        echo \frontend\themes\gentelella_v2\widgets\Menu::widget([
            'items' => $menuItems,
            'encodeLabels' => false,
            'activateParents' => true,
            'linkTemplate' => '<a href="{url}" {attributes}>{icon}<span>{label}</span>{badge}</a>'
        ]);

        function ensureVisibility(&$items)
        {
            $allVisible = false;
            foreach ($items as &$item) {
                if (isset($item['url']) && is_array($item['url']) && !isset($item['visible'])) {
                    $url = $item['url'][0];
                    if ($url === '/' || Yii::$app->user->can('/*')) {
                        //for superAdmin or Dashboard
                        $item['visible'] = true;
                    } else {
                        // for ex.: /rbac/route  =>   rbac/route  for search in app->urlManager->rules
                        $patternForRulesFromMainConfig =  substr($url, 1, strlen($url));
                        $rulesFromMainConfig =\yii\helpers\ArrayHelper::map(Yii::$app->urlManager->rules, 'name', 'route');
                        foreach ($rulesFromMainConfig as $pattern => $route) {
                            if ($patternForRulesFromMainConfig === $pattern || strpos($pattern, $patternForRulesFromMainConfig . '/<') === 0) {
                                $item['visible'] = Yii::$app->user->can('/' . $route);
                                break;
                            }
                        }
                        if (!isset($item['visible'])) {
                            $item['visible'] = false;
                            $chunks = explode('/', $url);
                            $tmpRoute = '';
                            foreach ($chunks as $chunk) {
                                if (!empty($chunk)) {
                                    $tmpRoute .=  '/' . $chunk;
                                    if ($item['visible'] = Yii::$app->user->can($tmpRoute . '/*')) {
                                        break;
                                    }
                                }
                            }
                            if (!$item['visible']) {
                                $item['visible'] = Yii::$app->user->can($url);
                            }
                        }
                    }
                }
                // If not children are visible - make invisible this node
                if (isset($item['items']) && (!ensureVisibility($item['items']) && !isset($item['visible']))) {
                    $item['visible'] = false;
                }
                if (isset($item['label']) && (!isset($item['visible']) || $item['visible'] === true)) {
                    $allVisible = true;
                }
            }
            return $allVisible;
        }
        ?>

    </div>
</div>


<?php

$js =<<<JS
function updateCounters(url, className, idName) {
    var types = [];
    $("." + className).each(function(i) {
        types.push($(this).data('type'));
    });
    
    $.ajax({
        type: "POST",
        url: url,
        data: {types: types}, 
        dataType: 'json',
        success: function(data){
            if (typeof (data) != "undefined" && data != null) {
                $.each( data, function( key, val ) {
                    if (val != 0) {
                        $("#" + idName + "-" + key).html(val);
                    }
                });
            }
        },
        error: function(data){
            console.log(data);
        }, 
    });    
    
}
JS;
$this->registerJs($js, $this::POS_LOAD);

if (Yii::$app->user->can('leadSection')) {
    $urlBadgesCount = Url::to(['/badges/get-badges-count']);
    $this->registerJs("updateCounters('$urlBadgesCount', 'bginfo', 'badges');", $this::POS_LOAD);
}
if (Yii::$app->user->can('caseSection')) {
    $urlCasesQCount = Url::to(['/cases-q-counters/get-q-count']);
    $this->registerJs("updateCounters('$urlCasesQCount', 'cases-q-info', 'cases-q');", $this::POS_LOAD);
}
if (Yii::$app->user->can('/qa-task/qa-task-queue/count')) {
    $urlQaTaskCount = Url::to(['/qa-task/qa-task-queue/count']);
    $this->registerJs("updateCounters('$urlQaTaskCount', 'qa-task-info', 'qa-task-q');", $this::POS_LOAD);
}

$js = <<<JS
$('.nav.side-menu [data-ajax-link]').on('click', function (e) {
    e.preventDefault();
    let ajaxLink = $(this).data('ajax-link');
    let modalTitle = $(this).data('modal-title');
    
    if (ajaxLink) {
        let url = $(this).attr('href');
        
        var modal = $('#modal-md');
        $.ajax({
            type: 'post',
            url: url,
            data: {},
            dataType: 'html',
            beforeSend: function () {
                modal.find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
                modal.find('.modal-title').html(modalTitle);
                modal.modal('show');
            },
            success: function (data) {
                modal.find('.modal-body').html(data);
                modal.find('.modal-title').html(modalTitle);
                $('#preloader').addClass('d-none');
            },
            error: function () {
                new PNotify({
                    title: 'Error',
                    type: 'error',
                    text: 'Internal Server Error. Try again letter.',
                    hide: true
                });
                setTimeout(function () {
                    $('#modal-md').modal('hide');
                }, 300)
            },
        })
    }
});
JS;
$this->registerJs($js);
