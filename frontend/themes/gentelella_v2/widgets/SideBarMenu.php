<?php

/**
 * @author Alexandr <alex.connor@techork.com>
 */

namespace frontend\themes\gentelella_v2\widgets;

use common\models\Employee;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use sales\auth\Auth;
use Yii;

/**
 * Class SideBarMenu
 * @package frontend\themes\gentelella_v2\widgets
 */
class SideBarMenu extends \yii\bootstrap\Widget
{
    private static $instance;

    public ?Employee $user = null;

    /**
     * @return SideBarMenu
     */
    public static function getInstance(): SideBarMenu
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function init()
    {
        parent::init();
    }

    /**
     * @return string
     */
    public function run()
    {
        if ($this->user) {
            $user = $this->user;
        } else {
            $user = Yii::$app->user->identity;
        }

        $isAdmin = $user->isAdmin() || $user->isSuperAdmin();

        $isUM = $user->isUserManager();
        //$isSuperAdmin = $user->isSuperAdmin();

        $menuItems = [];
//        if ($user->canCall()) {
//            $menuItems[] = ['label' => 'Auto redial', 'url' => ['/call/auto-redial'], 'icon' => 'tty'];
//        }

        $menuItems[] = ['label' => 'Dashboard', 'url' => ['/dashboard/index'], 'icon' => 'area-chart'];

        $menuLItems = [];

        if (Auth::can('createLead')) {
            $menuLItems[] = ['label' => 'Create Lead', 'url' => ['/lead/create'], 'icon' => 'plus'];
        }
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



        $menuLItems[] = ['label' => 'Failed Bookings <span id="badges-failed-bookings" data-type="failed-bookings" class="label-success label pull-right bginfo"></span> ', 'url' => ['/queue/failed-bookings'], 'icon' => 'recycle'];
        $menuLItems[] = ['label' => 'Alternative <span id="badges-alternative" data-type="alternative" class="label-warning label pull-right bginfo"></span> ', 'url' => ['/queue/alternative'], 'icon' => 'recycle'];
        $menuLItems[] = ['label' => 'Bonus <span id="badges-bonus" data-type="bonus" class="label-success label pull-right bginfo"></span> ', 'url' => ['/queue/bonus'], 'icon' => 'recycle'];
        $menuLItems[] = ['label' => 'Follow Up <span id="badges-follow-up" data-type="follow-up" class="label-success label pull-right bginfo"></span> ', 'url' => ['/queue/follow-up'], 'icon' => 'recycle'];
        $menuLItems[] = ['label' => 'Processing <span id="badges-processing" data-type="processing" class="label-warning label pull-right bginfo"></span> ', 'url' => ['/queue/processing'], 'icon' => 'spinner'];
        $menuLItems[] = ['label' => 'Booked <span id="badges-booked" data-type="booked" class="label-success label pull-right bginfo"></span>', 'url' => ['/queue/booked'], 'icon' => 'flag-o text-warning'];
        $menuLItems[] = ['label' => 'Sold <span id="badges-sold" data-type="sold" class="label-success label pull-right bginfo"></span> ', 'url' => ['/queue/sold'], 'icon' => 'flag text-success'];
        $menuLItems[] = ['label' => 'Duplicate <span id="badges-duplicate" data-type="duplicate" class="label-danger label pull-right bginfo"></span>', 'url' => ['/queue/duplicate'], 'icon' => 'list text-danger'];
        $menuLItems[] = ['label' => 'Trash <span id="badges-trash" class="label-danger label pull-right"></span>', 'url' => ['/queue/trash'], 'icon' => 'trash-o text-danger'];
        $menuLItems[] = ['label' => 'Import Leads', 'url' => ['/lead/import'], 'icon' => 'upload'];


        if ($isAdmin) {
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
                'icon' => 'bell-o',
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
            $menuItems[] = [
                'label' => 'My Calls <span class="label-info label pull-right"></span> ',
                'url' => 'javascript:',
                'icon' => 'phone',
                'items' => [
                    ['label' => 'My Calls <span class="label-info label pull-right"></span> ', 'url' => ['/call/list'], 'icon' => 'phone'],
                    ['label' => 'My Calls Log <span class="label-info label pull-right"></span> ', 'url' => ['/call-log/list'], 'icon' => 'phone'],
                    ['label' => 'My Voice Mail Record <span id="voice-mail-record-count" data-type="count" class="label-success label pull-right voice-mail-record"></span> ', 'url' => ['/voice-mail-record/list'], 'icon' => 'envelope'],
                ]
            ];
        }

        $menuItems[] = [
            'label' => 'My Contacts <span id="call-inbox-queue" class="label-info label pull-right"></span>',
            'url' => ['/contacts/index'],
            'icon' => 'user'
        ];

        $menuItems[] = [
            'label' => 'Client Chat' . '<span id="div-cnt-client-chat"><span class="label-success label pull-right _cc_unread_messages"></span></span>',
            'url' => 'javascript:',
            'icon' => 'comments',
            'items' => [
                ['label' => 'My Client Chat v2', 'url' => ['/client-chat/dashboard-v2']],
                ['label' => 'Real Time Visitors', 'url' => ['/client-chat/real-time']],
            ]
        ];


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
                        ['label' => 'Log', 'url' => ['/call-log/index']],
                        ['label' => 'User access', 'url' => ['/call-log-user-access/index']],
                        ['label' => 'Cases', 'url' => ['/call-log-case/index']],
                        ['label' => 'Leads', 'url' => ['/call-log-lead/index']],
                        ['label' => 'Queue', 'url' => ['/call-log-queue/index']],
                        ['label' => 'Record', 'url' => ['/call-log-record/index']],
                        ['label' => 'Twilio Recording Log', 'url' => ['/call-recording-log-crud/index']],
                    ],
                ],
                [
                    'label' => 'Client Chat',
                    'url' => 'javascript:',
                    'icon' => 'comments warning',
                    'items' => [
                        ['label' => 'Project config', 'url' => ['/client-chat-project-config/index']],
                        ['label' => 'Client Chat CRUD', 'url' => ['/client-chat-crud/index']],
                        ['label' => 'Clients Chat', 'url' => ['/client-chat/index']],
                        ['label' => 'Request', 'url' => ['/client-chat-request-crud/index']],
                        ['label' => 'Channel', 'url' => ['/client-chat-channel-crud/index']],
                        ['label' => 'Channel Translate', 'url' => ['/client-chat-channel-translate/index']],
                        ['label' => 'Channel Transfer', 'url' => ['/client-chat-channel-transfer/index']],
                        ['label' => 'Status Log', 'url' => ['/client-chat-status-log-crud/index']],
                        ['label' => 'Action Reason', 'url' => ['/client-chat-action-reason-crud/index']],
                        ['label' => 'Status Log Reason', 'url' => ['/client-chat-status-log-reason-crud/index']],
                        ['label' => 'User Channel', 'url' => ['/client-chat-user-channel-crud/index']],
                        ['label' => 'User Access', 'url' => ['/client-chat-user-access-crud/index']],
                        ['label' => 'Chat Messages', 'url' => ['/client-chat-message-crud/index']],
                        ['label' => 'Chat Note', 'url' => ['/client-chat-note-crud/index']],
                        ['label' => 'Chat Leads', 'url' => ['/client-chat-lead/index']],
                        ['label' => 'Chat Cases', 'url' => ['/client-chat-case/index']],
                        ['label' => 'Visitor', 'url' => ['/client-chat-visitor-crud/index']],
                        ['label' => 'Visitor Data', 'url' => ['/client-chat-visitor-data-crud/index']],
                        ['label' => 'Client Chat QA', 'url' => ['/client-chat-qa/index']],
                        ['label' => 'Feedback', 'url' => ['/client-chat-feedback-crud/index']],
                        ['label' => 'Last Message', 'url' => ['/client-chat-last-message-crud/index']],
                        ['label' => 'Hold', 'url' => ['/client-chat-hold-crud/index']],
                        ['label' => 'Unread messages', 'url' => ['/client-chat-unread/index']],
                        ['label' => 'Connection Active chat', 'url' => ['/user-connection-active-chat/index']],
                        ['label' => 'Couch Note', 'url' => ['/client-chat-couch-note-crud/index']],
                        ['label' => 'Canned Response', 'url' => ['/client-chat-canned-response-crud/index']],
                        ['label' => 'Canned Response Category', 'url' => ['/client-chat-canned-response-category-crud/index']],
                        ['label' => 'Chat Forms', 'url' => ['/client-chat-form-crud/index']],
                        ['label' => 'User Chat Data CRUD', 'url' => ['/user-client-chat-data-crud/index']],
                        ['label' => 'User Chat Data Manage', 'url' => ['/user-client-chat-data/index']],
                    ],
                ],
                ['label' => 'SMS List', 'url' => ['/sms/index'], 'icon' => 'list'],
                ['label' => 'SMS Distrib List', 'url' => ['/sms-distribution-list/index'], 'icon' => 'list'],
                ['label' => 'Emails', 'url' => ['/email/index'], 'icon' => 'envelope'],
                ['label' => 'Notification List', 'url' => ['/notifications/index'], 'icon' => 'bell-o'],

                [
                    'label' => 'Conferences',
                    'url' => 'javascript:',
                    'icon' => 'comments',
                    'items' => [
                        ['label' => 'Conference Room', 'url' => ['/conference-room/index']],
                        ['label' => 'Conferences', 'url' => ['/conference/index']],
                        ['label' => 'Conference Event Log', 'url' => ['/conference-event-log/index']],
                        ['label' => 'Conference Debug', 'url' => ['/conference-debug/index']],
                        ['label' => 'Participants', 'url' => ['/conference-participant/index']],
                        ['label' => 'Participants Stats', 'url' => ['/conference-participant-stats/index']],
                        ['label' => 'Twilio Recording Log', 'url' => ['/conference-recording-log-crud/index']],
                    ]
                ],
                ['label' => 'Call Note', 'url' => ['/call-note-crud/index'], 'icon' => 'list'],
                ['label' => 'User Voice Mail', 'url' => ['/user-voice-mail/index'], 'icon' => 'microphone'],
                ['label' => 'Voice Mail Records', 'url' => ['/voice-mail-record/index'], 'icon' => 'envelope'],
                [
                    'label' => 'QCall',
                    'url' => 'javascript:',
                    'icon' => 'phone',
                    'items' => [
                        ['label' => 'Lead QCall List', 'url' => ['/lead-qcall/list']],
                        ['label' => 'Lead QCall All', 'url' => ['/lead-qcall/index']],
                        ['label' => 'QCall Config', 'url' => ['/qcall-config/index']],
                        ['label' => 'Project Weight', 'url' => ['/project-weight/index']],
                        ['label' => 'Status Weight', 'url' => ['/status-weight/index']],
                    ]
                ],
                ['label' => 'User Call Statuses', 'url' => ['/user-call-status/index'], 'icon' => 'list'],
                ['label' => 'Flight Segments', 'url' => ['/lead-flight-segment/index'], 'icon' => 'plane'],

                [
                    'label' => 'Quotes',
                    'url' => 'javascript:',
                    'icon' => 'list',
                    'items' => [
                        ['label' => 'Quote List', 'url' => ['/quotes/index'], 'icon' => 'list'],
                        ['label' => 'Quote Price List', 'url' => ['/quote-price/index'], 'icon' => 'list'],
                        ['label' => 'Flight Quote Label List', 'url' => ['/flight-quote-label-list-crud/index'], 'icon' => 'list'],
                        ['label' => 'Quote Label', 'url' => ['/quote-label-crud/index'], 'icon' => 'list'],
                    ],
                ],

                ['label' => 'Call User Access', 'url' => ['/call-user-access/index'], 'icon' => 'list'],
                [
                    'label' => 'Leads',
                    'url' => 'javascript:',
                    'icon' => 'list',
                    'items' => [
                        ['label' => 'Lead Call Experts', 'url' => ['/lead-call-expert/index']],
                        ['label' => 'Lead Status History', 'url' => ['/lead-flow/index']],
                        ['label' => 'Lead Check Lists', 'url' => ['/lead-checklist/index'], 'visible' => Yii::$app->user->can('manageLeadChecklist')],
                        ['label' => 'Checklist Status History', 'url' => ['/lead-flow-checklist/index'], 'visible' => Yii::$app->user->can('viewLeadFlowChecklist')],
                        ['label' => 'Duplicate Leads', 'url' => ['/leads/duplicate']],
                        ['label' => 'Export Leads', 'url' => ['/leads/export']],
                        ['label' => 'Lead Request', 'url' => ['/lead-request-crud/index']],
                        ['label' => 'Lead Data', 'url' => ['/lead-data-crud/index']],
                        ['label' => 'Lead Data Key', 'url' => ['/lead-data-key-crud/index']],
                    ]
                ]
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
                ['label' => 'Visitor Log', 'url' => ['/visitor-log/index'], 'icon' => 'list'],
                ['label' => 'User Contact Lists', 'url' => ['/user-contact-list/index'], 'icon' => 'list'],
                ['label' => 'Client Project', 'url' => ['/client-project/index'], 'icon' => 'list'],
                ['label' => 'Unsubscribed Clients', 'url' => ['/email-unsubscribe/index'], 'icon' => 'bell-slash'],
                ['label' => 'Stats', 'url' => ['/client/stats'], 'icon' => 'users'],
                ['label' => 'Client Accounts', 'url' => ['/client-account-crud/index'], 'icon' => 'user'],
                ['label' => 'Client Account Social', 'url' => ['/client-account-social-crud/index'], 'icon' => 'odnoklassniki'],
                ['label' => 'Client Visitor', 'url' => ['/client-visitor-crud/index'], 'icon' => 'comments'],
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
                ['label' => 'Product Holder', 'url' => ['/product/product-holder-crud/index']],
                ['label' => 'Product Options', 'url' => ['/product/product-option-crud/index']],
                ['label' => 'Product Quotes', 'url' => ['/product/product-quote-crud/index']],
                ['label' => 'Product Quotes Status Log', 'url' => ['/product/product-quote-status-log-crud/index']],
                ['label' => 'Product Quote Options', 'url' => ['/product/product-quote-option-crud/index']],
                ['label' => 'Product Quote Relation', 'url' => ['/product/product-quote-relation-crud/index']],
                ['label' => 'Product Quote Lead', 'url' => ['/product/product-quote-lead/index']],
                ['label' => 'Product Quote Origin', 'url' => ['/product/product-quote-origin/index']],

                ['label' => 'Orders', 'url' => 'javascript:', 'items' => [
                    ['label' => 'Orders', 'url' => ['/order/order-crud/index']],
                    ['label' => 'Orders Status Log', 'url' => ['/order/order-status-log-crud/index']],
                    ['label' => 'Orders User Profit', 'url' => ['/order/order-user-profit-crud/index']],
                    ['label' => 'Orders Tips', 'url' => ['/order/order-tips-crud/index']],
                    ['label' => 'Orders Tips User Profit', 'url' => ['/order/order-tips-user-profit-crud/index']],
                    ['label' => 'Order Process Manager', 'url' => ['/order/order-process-manager/index']],
                    ['label' => 'Order Request', 'url' => ['/order/order-request-crud/index']],
                    ['label' => 'Order Data', 'url' => ['/order/order-data-crud/index']],
                    ['label' => 'Case Order Relation', 'url' => ['/case-order-crud/index']],
                    ['label' => 'Lead Order Relation', 'url' => ['/lead-order-crud/index']],
                    ['label' => 'Lead Product Relation', 'url' => ['/lead-product-crud/index']],
                    ['label' => 'Order Contact', 'url' => ['/order/order-contact-crud/index']],
                    ['label' => 'Order Email', 'url' => ['/order/order-email-crud/index']],
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
                ['label' => 'Sale Credit Cards', 'url' => ['/sale-credit-card/index']],
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

                [
                    'label' => 'Projects',
                    'url' => 'javascript:',
                    'icon' => 'product-hunt',
                    'items' => [
                        ['label' => 'Projects', 'url' => ['/project/index']],
                        ['label' => 'Project Sources', 'url' => ['/sources/index']],
                        ['label' => 'Project Settings', 'url' => ['/settings/projects']],
                        ['label' => 'Project Locales', 'url' => ['/project-locale/index']],
                        ['label' => 'Project Relation', 'url' => ['/project-relation-crud/index']],
                        ['label' => 'App Project key', 'url' => ['/app-project-key-crud/index']],
                    ]
                ],

                [
                    'label' => 'Departments',
                    'url' => 'javascript:',
                    'icon' => 'sitemap',
                    'items' => [
                        ['label' => 'Departments', 'url' => ['/department/index']],
                        ['label' => 'Department Emails', 'url' => ['/department-email-project/index']],
                        ['label' => 'Department Phones', 'url' => ['/department-phone-project/index']],
                        ['label' => 'Department Phones Import', 'url' => ['/department-phone-project/import']],
                    ]
                ],
                [
                    'label' => 'Phone Line',
                    'url' => 'javascript:',
                    'icon' => 'phone-volume',
                    'items' => [
                        ['label' => 'Phone Line', 'url' => ['/phone-line-crud/index']],
                        ['label' => 'Phone Number', 'url' => ['/phone-line-phone-number-crud/index']],
                        ['label' => 'User Assign', 'url' => ['/phone-line-user-assign-crud/index']],
                        ['label' => 'User Group', 'url' => ['/phone-line-user-group-crud/index']],
                        ['label' => 'User Personal Phone Number', 'url' => ['/user-personal-phone-number-crud/index']],
                    ]
                ],
                [
                    'label' => 'Call command',
                    'url' => 'javascript:',
                    'icon' => 'list',
                    'items' => [
                        ['label' => 'Call Command', 'url' => ['/call-command/index']],
                        ['label' => 'Phone Line Commands', 'url' => ['/phone-line-command-crud/index']],
                        ['label' => 'Call Gather Switches', 'url' => ['/call-gather-switch-crud/index']],
                    ]
                ],


                ['label' => 'Phone List', 'url' => ['/phone-list/index'], 'icon' => 'phone'],
                ['label' => 'Phone Blacklist', 'url' => ['/phone-blacklist/index'], 'icon' => 'phone'],
                ['label' => 'Phone Blacklist Log', 'url' => ['/phone-blacklist-log-crud/index'], 'icon' => 'phone'],
                ['label' => 'Email List', 'url' => ['/email-list/index'], 'icon' => 'envelope-o'],

                //['label' => 'Airlines', 'url' => ['/settings/airlines'], 'icon' => 'plane'],
                ['label' => 'Airlines', 'url' => ['/airline-crud/index'], 'icon' => 'space-shuttle'],
                ['label' => 'Airports', 'url' => ['/airports/index'], 'icon' => 'plane'],
                ['label' => 'Airport Lang', 'url' => ['/airport-lang-crud/index'], 'icon' => 'language'],

                ['label' => 'ACL (IP)', 'url' => ['/settings/acl'], 'icon' => 'user-secret'],
                ['label' => 'API Users', 'url' => ['/api-user/index'], 'icon' => 'users'],
                ['label' => 'Tasks', 'url' => ['/task/index'], 'icon' => 'list'],
                ['label' => 'Lead Tasks', 'url' => ['/lead-task/index'], 'icon' => 'list'],

                ['label' => 'Check List Types', 'url' => ['/lead-checklist-type/index'], 'icon' => 'list', 'visible' => Yii::$app->user->can('manageLeadChecklistType')],
                ['label' => 'Coupons', 'url' => ['/coupon/index'], 'icon' => 'list'],

                [
                    'label' => 'Cases & Sale',
                    'url' => 'javascript:',
                    'icon' => 'list',
                    'items' => [
                        ['label' => 'Case Sales', 'url' => ['/case-sale/index'], 'icon' => 'list'],
                        ['label' => 'Case Notes', 'url' => ['/case-note/index'], 'icon' => 'list'],
                        ['label' => 'Case status history', 'url' => ['/case-status-log/index'], 'icon' => 'bars'],
                        ['label' => 'Case categories', 'url' => ['/case-category/index'], 'icon' => 'list'],
                        ['label' => 'Coupon Cases', 'url' => ['/coupon-case/index'], 'icon' => 'list'],
                        ['label' => 'Sale Ticket', 'url' => ['/sale-ticket/index'], 'icon' => 'list'],
                    ]
                ],

                [
                    'label' => 'Template types',
                    'url' => 'javascript:',
                    'icon' => 'list',
                    'items' => [
                        ['label' => 'Email template types', 'url' => ['/email-template-type/index']],
                        ['label' => 'SMS template types', 'url' => ['/sms-template-type/index']],
                    ]
                ],

                ['label' => 'Call recording disabled', 'url' => ['/call-recording-disabled/list'], 'icon' => 'list'],

                [
                    'label' => 'Shift Schedules',
                    'url' => 'javascript:',
                    'icon' => 'calendar',
                    'items' => [
                        ['label' => 'Shift', 'url' => ['/shift-crud/index']],
                        ['label' => 'Shift Schedule Rule', 'url' => ['/shift-schedule-rule-crud/index']],
                        ['label' => 'User Shift Assign', 'url' => ['/user-shift-assign-crud/index']],
                        ['label' => 'User Shift Schedule', 'url' => ['/user-shift-schedule-crud/index']],
                    ]
                ],

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
                ['label' => 'User Commission Rules', 'url' => ['/user-commission-rules-crud/index'], 'icon' => 'list'],
                ['label' => 'User Bonus Rules', 'url' => ['/user-bonus-rules-crud/index'], 'icon' => 'list'],
                ['label' => 'User Failed Login', 'url' => ['/user-failed-login/index'], 'icon' => 'list'],
                ['label' => 'User Monitor', 'url' => ['/user-monitor/index'], 'icon' => 'list'],
                ['label' => 'User Monitor Stats', 'url' => ['/user-monitor/stats'], 'icon' => 'list'],
                [
                    'label' => 'User model setting crud',
                    'url' => ['/user-model-setting-crud/index'],
                    'icon' => 'list',
                ],
            ]
        ];

        $menuItems[] = [
            'label' => 'Orders',
            'url' => 'javascript:',
            'icon' => 'shopping-cart',
            'items' => [
                ['label' => 'Search Orders', 'url' => ['/order/order/search'], 'icon' => 'search'],
                ['label' => 'Error Orders', 'url' => ['/order/order/error-list'], 'icon' => 'exclamation-triangle'],
                ['label' => 'New <span id="order-q-new" data-type="new" class="label-warning label pull-right order-q-info"></span>', 'url' => ['/order/order-q/new'], 'icon' => 'paste text-warning'],
                ['label' => 'Pending <span id="order-q-pending" data-type="pending" class="label-info label pull-right order-q-info"></span>', 'url' => ['/order/order-q/pending'], 'icon' => 'briefcase text-info'],
                ['label' => 'Processing <span id="order-q-processing" data-type="processing" class="label-info label pull-right order-q-info"></span>', 'url' => ['/order/order-q/processing'], 'icon' => 'spinner'],
                ['label' => 'Prepared <span id="order-q-prepared" data-type="prepared" class="label-success label pull-right order-q-info"></span>', 'url' => ['/order/order-q/prepared'], 'icon' => 'recycle'],
                ['label' => 'Complete <span id="order-q-complete" data-type="complete" class="label-success label pull-right order-q-info"></span>', 'url' => ['/order/order-q/complete'], 'icon' => 'flag text-success'],
                ['label' => 'Cancel Processing <span id="order-q-cancel-processing" data-type="cancel-processing" class="label-danger label pull-right order-q-info"></span>', 'url' => ['/order/order-q/cancel-processing'], 'icon' => 'spinner text-danger'],
                ['label' => 'Error <span id="order-q-error" data-type="error" class="label-danger label pull-right order-q-info"></span>', 'url' => ['/order/order-q/error'], 'icon' => 'exclamation-triangle text-danger'],
                ['label' => 'Declined <span id="order-q-declined" data-type="declined" class="label-danger label pull-right order-q-info"></span>', 'url' => ['/order/order-q/declined'], 'icon' => 'reply'],
                ['label' => 'Canceled <span id="order-q-canceled" data-type="canceled" class="label-danger label pull-right order-q-info"></span>', 'url' => ['/order/order-q/canceled'], 'icon' => 'close'],
                ['label' => 'Canceled failed <span id="order-q-canceled-failed" data-type="canceled-failed" class="label-danger label pull-right order-q-info"></span>', 'url' => ['/order/order-q/cancel-failed'], 'icon' => 'close '],
            ]
        ];

        $menuModuleItems = [];

        if (class_exists('\modules\attraction\AttractionModule')) {
            $menuModuleItems[] = [
                'label' => 'Attraction module',
                'url' => 'javascript:',
                'icon' => 'plane',
                'items' => \modules\attraction\AttractionModule::getListMenu()
            ];
        }

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

        if (class_exists('\modules\fileStorage\FileStorageModule')) {
            $menuModuleItems[] = [
                'label' => 'File storage',
                'url' => 'javascript:',
                'icon' => 'list',
                'items' => \modules\fileStorage\FileStorageModule::getListMenu()
            ];
        }

        if (class_exists('\modules\rentCar\RentCarModule')) {
            $menuModuleItems[] = [
                'label' => 'Rent Car module',
                'url' => 'javascript:',
                'icon' => 'car',
                'items' => \modules\rentCar\RentCarModule::getListMenu()
            ];
        }

        if (class_exists('\modules\cruise\CruiseModule')) {
            $menuModuleItems[] = [
                'label' => 'Cruise',
                'url' => 'javascript:',
                'icon' => 'ship',
                'items' => \modules\cruise\CruiseModule::getListMenu()
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
                ['label' => 'Pending <span id="qa-task-q-pending" data-type="pending" class="badge badge-' . QaTaskStatus::getCssClass(QaTaskStatus::PENDING) . ' pull-right qa-task-info"></span>', 'url' => ['/qa-task/qa-task-queue/pending']],
                ['label' => 'Processing <span id="qa-task-q-processing" data-type="processing" class="badge badge-' . QaTaskStatus::getCssClass(QaTaskStatus::PROCESSING) . ' pull-right qa-task-info"></span>', 'url' => ['/qa-task/qa-task-queue/processing']],
                ['label' => 'Escalated <span id="qa-task-q-escalated" data-type="escalated" class="badge badge-' . QaTaskStatus::getCssClass(QaTaskStatus::ESCALATED) . ' pull-right qa-task-info"></span>', 'url' => ['/qa-task/qa-task-queue/escalated']],
                ['label' => 'Closed <span id="qa-task-q-closed" data-type="closed" class="badge badge-' . QaTaskStatus::getCssClass(QaTaskStatus::CLOSED) . ' pull-right qa-task-info"></span>', 'url' => ['/qa-task/qa-task-queue/closed']],
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
                ['label' => 'Case Categories Report', 'url' => ['/case-category/report'], 'icon' => 'table'],
                ['label' => 'Clients Chat Report', 'url' => ['/client-chat/report'], 'icon' => 'table'],
                ['label' => 'Calls Stats', 'url' => ['/stats/calls-graph'], 'icon' => 'line-chart'],
                ['label' => 'SMS Stats', 'url' => ['/stats/sms-graph'], 'icon' => 'line-chart'],
                ['label' => 'Emails Stats', 'url' => ['/stats/emails-graph'], 'icon' => 'line-chart'],
                ['label' => 'Clients Chat Stats', 'url' => ['/client-chat/stats'], 'icon' => 'line-chart'],
                ['label' => 'Chat Extended Stats', 'url' => ['/client-chat/extended-stats'], 'icon' => 'line-chart'],
                ['label' => 'Chat Feedback Stats', 'url' => ['/client-chat/feedback-stats'], 'icon' => 'star'],
                ['label' => 'Stats Employees', 'url' => ['/stats/index'], 'icon' => 'users'],
                ['label' => 'User Stats', 'url' => ['/user-connection/stats'], 'icon' => 'area-chart'],
                ['label' => 'Call User Map', 'url' => ['/call/user-map'], 'icon' => 'map'],
                ['label' => 'Real-time User Map', 'url' => ['/call/realtime-user-map'], 'icon' => 'map'],
                ['label' => 'Realtime Map v2', 'url' => ['/call/realtime-map'], 'icon' => 'map'],
                ['label' => 'Client Chat Monitor', 'url' => ['/client-chat/monitor'], 'icon' => 'map'],
                ['label' => 'Agents Ratings', 'url' => ['/stats/agent-ratings'], 'icon' => 'star-half-empty'],
                ['label' => 'Stats Agents & Leads', 'url' => ['/report/agents'], 'icon' => 'users'],
                [
                    'label' => 'Monitor', 'url' => 'javascript:', 'icon' => 'map',
                    'items' => [
                        ['label' => Yii::t('menu', 'Incoming Call'), 'url' => ['/monitor/call-incoming']],
                    ]
                ],
                ['label' => 'User Stats dashboard', 'url' => ['/user-stats/index'], 'icon' => 'users'],
                ['label' => 'User Sales', 'url' => ['/sales/index'], 'icon' => 'money'],
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
                        ['label' => Yii::t('languages', 'Languages'), 'url' => ['/language/index']],
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
                        ['label' => Yii::t('menu', 'Check Flight Dump'), 'url' => ['/tools/check-flight-dump']],
                        ['label' => Yii::t('menu', 'Check Exclude IP'), 'url' => ['/tools/check-exclude-ip']],
                        ['label' => Yii::t('menu', 'Stash Log Files'), 'url' => ['/tools/stash-log-file']],
                        ['label' => Yii::t('menu', 'DB Info'), 'url' => ['/tools/db-info']],
                    ]
                ],

                $menuLanguages,

                ['label' => 'Site Settings Category', 'url' => ['/setting-category/index'], 'icon' => 'list'],
                ['label' => 'Site Settings', 'url' => ['/setting/index'], 'icon' => 'cogs'],
                ['label' => 'Virtual cron', 'url' => ['/virtual-cron/cron-scheduler/index'], 'icon' => 'cogs'],
                ['label' => 'Site ENV', 'url' => ['/setting/env'], 'icon' => 'info-circle'],
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

        $menuItems[] = [
            'label' => 'ABAC',
            'url' => 'javascript:',
            'icon' => 'cogs',
            'items' => [
                ['label' => 'ABAC Policy List', 'url' => ['/abac/abac-policy']],
                ['label' => 'Policy List Content', 'url' => ['/abac/abac-policy/list-content']],
                ['label' => 'ABAC Doc', 'url' => ['/abac/abac-doc/index']],
            ],
        ];

        if ($search_text = Yii::$app->request->get('search_text')) {
            self::filterMenuItems($menuItems, $search_text);
        }
        self::ensureVisibility($menuItems);



        return $this->render('side_bar_menu', ['menuItems' => $menuItems, 'user' => $user, 'search_text' => $search_text]);
    }

    /**
     * @param $items
     * @param string $text
     * @return bool
     */
    public static function filterMenuItems(&$items, string $text = ''): bool
    {
        $allVisible = false;
        foreach ($items as $k => &$item) {
            if (!isset($item['label'])) {
                $item['visible'] = false;
                continue;
            }

            if (stripos(strip_tags($item['label']), $text) === false) {
                $item['visible'] = false;
            }

            if (isset($item['items'])) {
                $item['visible'] = self::filterMenuItems($item['items'], $text);
            }

            if (!isset($item['visible']) || $item['visible'] === true) {
                $allVisible = true;
            }
        }
        return $allVisible;
    }

    /**
     * @param $items
     * @return bool
     */
    public static function ensureVisibility(&$items): bool
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
                    $rulesFromMainConfig = \yii\helpers\ArrayHelper::map(Yii::$app->urlManager->rules, 'name', 'route');
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
            if (isset($item['items']) && (!self::ensureVisibility($item['items']) && !isset($item['visible']))) {
                $item['visible'] = false;
            }
            if (isset($item['label']) && (!isset($item['visible']) || $item['visible'] === true)) {
                $allVisible = true;
            }
        }
        return $allVisible;
    }
}
