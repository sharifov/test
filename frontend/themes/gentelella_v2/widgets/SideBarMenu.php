<?php

/**
 * @author Alexandr <alex.connor@techork.com>
 */

namespace frontend\themes\gentelella_v2\widgets;

use common\models\Employee;
use modules\featureFlag\FFlag;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use modules\lead\src\abac\queue\LeadBusinessExtraQueueAbacObject;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\services\UserShiftScheduleService;
use src\auth\Auth;
use modules\user\userFeedback\abac\dto\UserFeedbackAbacDto;
use modules\user\userFeedback\abac\UserFeedbackAbacObject;
use src\helpers\app\AppHelper;
use src\services\lead\LeadBusinessExtraQueueService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

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

        if (Yii::$app->user->can('/phone-device-log/index')) {
            $menuItems[] = [
                'label' => 'VoIP Data',
                'url' => 'javascript:',
                'icon' => 'phone',
                'items' => [
//                    ['label' => 'VoIP / Phone Device',
//                        'url' => ['/voip/index'],
//                        'icon' => 'phone-square',
//                        'visible' => Yii::$app->user->can('PhoneWidget'),
//                        'title' => 'VoIP Phone Device'
//                    ],
                    ['label' => 'Device List',
                        'url' => ['/phone-device-crud/index'],
                        'icon' => 'list',
                        'title' => 'VoIP Phone Devices CRUD'],
                    ['label' => 'Phone Device Logs',
                        'url' => ['/phone-device-log/index'],
                        'icon' => 'list',
                        'title' => 'VoIP Phone Devices Log']
                ],
            ];
        }
//        else {
//            $menuItems[] = [
//                'label' => 'VoIP / Phone Device',
//                'title' => 'VoIP Phone Device',
//                'url' => ['/voip/index'],
//                'icon' => 'phone-square',
//                'visible' => Yii::$app->user->can('PhoneWidget')
//            ];
//        }

        /** @abac null, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_CREATE, Access to create lead */
        $menuLItems[] = [
            'label' => 'Create Lead',
            'url' => ['/lead/create'],
            'icon' => 'plus',
            'abac' => [
                'dto' => null,
                'object' => LeadAbacObject::OBJ_LEAD,
                'action' => LeadAbacObject::ACTION_CREATE
            ],
        ];

        $menuLItems[] = ['label' => 'Create New Lead', 'url' => ['/lead/create2'], 'icon' => 'plus', 'attributes' => ['data-ajax-link' => true, 'data-modal-title' => 'Create New Lead']];

        $menuLItems[] = ['label' => 'Search Leads', 'url' => ['/leads/index'], 'icon' => 'search'];

        $menuLItems[] = ['label' => 'Lead Redial <span id="badges-redial" data-type="redial" class="label-info label pull-right bginfo text"></span>', 'url' => ['/lead-redial/index'], 'icon' => 'phone'];

        $menuLItems[] = ['label' => 'New', 'url' => ['/lead/new'], 'icon' => 'paste text-warning'];

        $menuLItems[] = ['label' => 'Pending <span id="badges-pending" data-type="pending" class="label-info label pull-right bginfo"></span> ', 'url' => ['/queue/pending'], 'icon' => 'briefcase'];
        $menuLItems[] = ['label' => 'Business Inbox <span id="badges-business-inbox" data-type="business-inbox" class="label-info label pull-right bginfo"></span> ', 'url' => ['/queue/business-inbox'], 'icon' => 'ioxhost text-info'];

        if (isset(Yii::$app->params['settings']['enable_lead_inbox']) && Yii::$app->params['settings']['enable_lead_inbox']) {
            $menuLItems[] = ['label' => 'Inbox <span id="badges-inbox" data-type="inbox" class="label-info label pull-right bginfo"></span> ', 'url' => ['/queue/inbox'], 'icon' => 'briefcase'];
        }

        /** @abac $leadAbacDto, LeadAbacObject::OBJ_EXTRA_QUEUE, LeadAbacObject::ACTION_ACCESS, show extra-queue in menu */
        $menuLItems[] = [
            'label' => 'Extra Queue <span id="badges-extra-queue" data-type="extra-queue" class="label-success label pull-right bginfo"></span> ',
            'url' => ['/lead/extra-queue'],
            'icon' => 'history text-success',
            'title' => 'Extra queue',
            'abac' => [
                'dto' => new LeadAbacDto(null, (int) Auth::id()),
                'object' => LeadAbacObject::OBJ_EXTRA_QUEUE,
                'action' => LeadAbacObject::ACTION_ACCESS
            ],
        ];

        $menuLItems[] = [
            'label' => 'Business Extra Queue <span id="badges-business-extra-queue" data-type="business-extra-queue" class="label-success label pull-right bginfo"></span>',
            'url' => ['/lead/business-extra-queue'],
            'icon' => 'history text-success',
            'visible' => LeadBusinessExtraQueueService::canAccess(),
        ];

        $menuLItems[] = ['label' => 'Failed Bookings <span id="badges-failed-bookings" data-type="failed-bookings" class="label-success label pull-right bginfo"></span> ',
            'url' => ['/queue/failed-bookings'], 'icon' => 'recycle', 'title' => 'Failed Bookings Leads queue'];
        $menuLItems[] = ['label' => 'Alternative <span id="badges-alternative" data-type="alternative" class="label-warning label pull-right bginfo"></span> ',
            'url' => ['/queue/alternative'], 'icon' => 'recycle', 'title' => 'Alternative Leads queue'];
        $menuLItems[] = ['label' => 'Bonus <span id="badges-bonus" data-type="bonus" class="label-success label pull-right bginfo"></span> ',
            'url' => ['/queue/bonus'], 'icon' => 'recycle', 'title' => 'Bonus Leads queue'];
        $menuLItems[] = ['label' => 'Follow Up <span id="badges-follow-up" data-type="follow-up" class="label-success label pull-right bginfo"></span> ',
            'url' => ['/queue/follow-up'], 'icon' => 'recycle', 'title' => 'Follow Up Leads queue'];
        $menuLItems[] = ['label' => 'Processing <span id="badges-processing" data-type="processing" class="label-warning label pull-right bginfo"></span> ',
            'url' => ['/queue/processing'], 'icon' => 'spinner', 'title' => 'Processing Leads queue'];

        /** @abac $leadAbacDto, LeadAbacObject::OBJ_CLOSED_QUEUE, LeadAbacObject::ACTION_ACCESS, show closed-queue in menu */
        $menuLItems[] = [
            'label' => 'Closed Queue <span id="badges-closed" data-type="closed" class="label-danger label pull-right bginfo"></span> ',
            'url' => ['/lead/closed'],
            'icon' => 'times text-danger',
            'title' => 'Closed queue',
            'abac' => [
                'dto' => new LeadAbacDto(null, (int) Auth::id()),
                'object' => LeadAbacObject::OBJ_CLOSED_QUEUE,
                'action' => LeadAbacObject::ACTION_ACCESS
            ],
        ];

        $menuLItems[] = ['label' => 'Booked <span id="badges-booked" data-type="booked" class="label-success label pull-right bginfo"></span>',
            'url' => ['/queue/booked'], 'icon' => 'flag-o text-warning', 'title' => 'Booked Leads queue'];
        $menuLItems[] = ['label' => 'Sold <span id="badges-sold" data-type="sold" class="label-success label pull-right bginfo"></span> ',
            'url' => ['/queue/sold'], 'icon' => 'flag text-success', 'title' => 'Sold Leads queue'];
        $menuLItems[] = ['label' => 'Duplicate <span id="badges-duplicate" data-type="duplicate" class="label-danger label pull-right bginfo"></span>',
            'url' => ['/queue/duplicate'], 'icon' => 'list text-danger', 'title' => 'Duplicate Leads queue'];
        $menuLItems[] = ['label' => 'Trash <span id="badges-trash" class="label-danger label pull-right"></span>',
            'url' => ['/queue/trash'], 'icon' => 'trash-o text-danger', 'title' => 'Trash Leads queue'];
        $menuLItems[] = ['label' => 'Import Leads',
            'url' => ['/lead/import'], 'icon' => 'upload', 'title' => 'Import Leads'];


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

        $menuCases[] = ['label' => 'Need Action <span id="cases-q-need-action" data-type="need-action" class="label-warning label pull-right cases-q-info"></span> ',
            'url' => ['/cases-q/need-action'], 'icon' => 'list', 'title' => 'Case Need Action queue'];
        $menuCases[] = ['label' => 'Pending <span id="cases-q-pending" data-type="pending" class="label-warning label pull-right cases-q-info"></span> ',
            'url' => ['/cases-q/pending'], 'icon' => 'list', 'title' => 'Case Pending queue'];
        $menuCases[] = ['label' => 'Inbox <span id="cases-q-inbox" data-type="inbox" class="label-warning label pull-right cases-q-info"></span> ',
            'url' => ['/cases-q/inbox'], 'icon' => 'briefcase text-info', 'title' => 'Case Inbox queue'];
        $menuCases[] = ['label' => 'Unidentified <span id="cases-q-unidentified" data-type="unidentified" class="label-warning label pull-right cases-q-info"></span> ',
            'url' => ['/cases-q/unidentified'], 'icon' => 'list', 'title' => 'Case Unidentified queue'];
        $menuCases[] = ['label' => 'First Priority <span id="cases-q-first-priority" data-type="first-priority" class="label-warning label pull-right cases-q-info"></span> ',
            'url' => ['/cases-q/first-priority'], 'icon' => 'list', 'title' => 'Case First Priority queue'];
        $menuCases[] = ['label' => 'Second Priority <span id="cases-q-second-priority" data-type="second-priority" class="label-warning label pull-right cases-q-info"></span> ',
            'url' => ['/cases-q/second-priority'], 'icon' => 'list', 'title' => 'Case Second Priority queue'];
        $menuCases[] = ['label' => 'Pass Departure <span id="cases-q-pass-departure" data-type="pass-departure" class="label-warning label pull-right cases-q-info"></span> ',
            'url' => ['/cases-q/pass-departure'], 'icon' => 'list', 'title' => 'Case Pass Departure queue'];
        $menuCases[] = ['label' => 'Processing <span id="cases-q-processing" data-type="processing" class="label-warning label pull-right cases-q-info"></span> ',
            'url' => ['/cases-q/processing'], 'icon' => 'spinner', 'title' => 'Case Processing queue'];
        $menuCases[] = ['label' => 'Follow Up <span id="cases-q-follow-up" data-type="follow-up" class="label-success label pull-right cases-q-info"></span> ',
            'url' => ['/cases-q/follow-up'], 'icon' => 'recycle', 'title' => 'Case Follow Up queue'];
        $menuCases[] = ['label' => 'Awaiting <span id="cases-q-awaiting" data-type="awaiting" class="label-success label pull-right cases-q-info"></span> ',
            'url' => ['/cases-q/awaiting'], 'icon' => 'hourglass', 'title' => 'Case Awaiting queue'];
        $menuCases[] = ['label' => 'Auto Processing <span id="cases-q-auto-processing" data-type="auto-processing" class="label-success label pull-right cases-q-info"></span> ',
            'url' => ['/cases-q/auto-processing'], 'icon' => 'spinner text-success', 'title' => 'Case Auto Processing queue'];
        $menuCases[] = ['label' => 'Solved <span id="cases-q-solved" data-type="solved" class="label-success label pull-right cases-q-info"></span> ',
            'url' => ['/cases-q/solved'], 'icon' => 'flag', 'title' => 'Case Solved queue'];
        $menuCases[] = ['label' => 'Error <span id="cases-q-error" data-type="error" class="label-danger label pull-right cases-q-info"></span>',
            'url' => ['/cases-q/error'], 'icon' => 'times text-danger', 'title' => 'Case Error queue'];
        $menuCases[] = ['label' => 'Trash <span id="cases-q-trash" class="label-danger label pull-right"></span>',
            'url' => ['/cases-q/trash'], 'icon' => 'trash-o text-danger', 'title' => 'Case Trash queue'];

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

        /** @abac ShiftAbacObject::ACT_MY_SHIFT_SCHEDULE, ShiftAbacObject::ACTION_ACCESS, Access menu My Shift Schedule */
        $shiftMenuItems[] = [
            'label' => 'My Shift',
            'url' => ['/shift-schedule/index'],
            'icon' => 'calendar',
            'abac'  => [
                'dto'    => null,
                'object' => ShiftAbacObject::ACT_MY_SHIFT_SCHEDULE,
                'action' => ShiftAbacObject::ACTION_ACCESS,
            ],
        ];


        $shiftMenuItems[] = [
            'label' => 'Schedule Event Requests',
            'url' => ['/shift/user-shift-schedule-request/index'],
            'icon' => 'calendar-o',
            'title' => 'User Shift Schedule Event Request',
        ];

        $shiftMenuItems[] = [
            'label' => 'User Shift Calendar',
            'url' => ['/shift-schedule/calendar'],
            'icon' => 'calendar text-warning',
            'title' => 'User Shift Schedule Calendar',
            'abac'  => [
                'dto'    => null,
                'object' => ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR,
                'action' => ShiftAbacObject::ACTION_ACCESS,
            ],
        ];


        $menuItems[] = [
            'label' => 'Shift Schedule', //  <sup style="color: red">NEW</sup>
            'url' => 'javascript:',
            'icon' => 'calendar',
            'items' => $shiftMenuItems
        ];



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
            'label' => 'Email Data<span class="label-info label pull-right"></span> ',
            'url' => 'javascript:',
            'icon' => 'envelope',
            'items' => [
                ['label' => 'Emails', 'url' => ['/email/index'], 'icon' => 'envelope'],
                ['label' => 'Review Queue All', 'url' => ['/email-review-queue/index'], 'icon' => 'list'],
                ['label' => 'Review Queue Pending', 'url' => ['/email-review-queue/pending'], 'icon' => 'list'],
                ['label' => 'Review Queue Completed', 'url' => ['/email-review-queue/completed'], 'icon' => 'list'],
                ['label' => 'Email List', 'url' => ['/email-list/index'], 'icon' => 'envelope-o'],
                ['label' => 'Email Review Queue Crud', 'url' => ['/email-review-queue-crud/index'], 'icon' => 'list'],
                ['label' => 'Email Quote Crud', 'url' => ['/email-quote-crud/index'], 'icon' => 'list'],
            ]
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
                [
                    'label' => 'Calls',
                    'url' => 'javascript:',
                    'icon' => 'folder',
                    'items' => [
                        ['label' => 'Call List', 'url' => ['/call/index'], 'icon' => 'phone', 'title' => 'Call list'],
                        ['label' => 'User Voice Mail', 'url' => ['/user-voice-mail/index'], 'icon' => 'microphone', 'Call User Voice Mail'],
                        ['label' => 'Voice Mail Records', 'url' => ['/voice-mail-record/index'], 'icon' => 'envelope', 'title' => 'Call Voice Mail Records'],
                        ['label' => 'User Call Statuses', 'url' => ['/user-call-status/index'], 'icon' => 'list'],
                        ['label' => 'Call Notes', 'url' => ['/call-note-crud/index']],
                    ],
                ],
                [
                    'label' => 'Call logs',
                    'url' => 'javascript:',
                    'icon' => 'folder',
                    'items' => [
                        ['label' => 'Call Log', 'url' => ['/call-log/index'], 'title' => 'Call Logs'],
                        ['label' => 'User access', 'url' => ['/call-log-user-access/index'], 'title' => 'Call Logs User access'],
                        ['label' => 'Cases', 'url' => ['/call-log-case/index'], 'title' => 'Call Logs Cases'],
                        ['label' => 'Leads', 'url' => ['/call-log-lead/index'], 'title' => 'Call Logs Leads'],
                        ['label' => 'Queue', 'url' => ['/call-log-queue/index'], 'title' => 'Call Logs Queue'],
                        ['label' => 'Record', 'url' => ['/call-log-record/index'], 'title' => 'Call Logs Record'],
                        ['label' => 'Twilio Recording Log', 'url' => ['/call-recording-log-crud/index'], 'title' => 'Call Logs Twilio Recording'],
                        ['label' => 'Filter Guard', 'url' => ['/call-log-filter-guard-crud/index'], 'title' => 'Call Logs Filter Guard'],
                    ],
                ],
                [
                    'label' => 'Client Chat',
                    'url' => 'javascript:',
                    'icon' => 'folder',
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
                        ['label' => 'Chat Survey', 'url' => ['/client-chat-survey/index']],
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
                        ['label' => 'Client Chat Component Event', 'url' => ['/client-chat-component-event/index']],
                        ['label' => 'Client Chat Component Event CRUD', 'url' => ['/client-chat-component-event-crud/index']],
                        ['label' => 'Client Chat Component Rule CRUD', 'url' => ['/client-chat-component-rule-crud/index']],
                        ['label' => 'Client Chat Data Request CRUD', 'url' => ['/client-chat-data-request-crud/index']],
                    ],
                ],
                [
                    'label' => 'SMS',
                    'url' => 'javascript:',
                    'icon' => 'commenting',
                    'items' => [
                        ['label' => 'SMS List', 'url' => ['/sms/index'], 'icon' => 'list'],
                        ['label' => 'SMS Distrib List', 'url' => ['/sms-distribution-list/index'], 'icon' => 'list'],
                        ['label' => 'Sms Subscribe Crud', 'url' => ['/sms-subscribe-crud/index']],
                    ]
                ],
                ['label' => 'Notification List', 'url' => ['/notifications/index'], 'icon' => 'bell-o'],
                [
                    'label' => 'Currency',
                    'url' => 'javascript:',
                    'icon' => 'folder',
                    'items' => [
                        ['label' => 'Currency List', 'url' => ['/currency/index'], 'title' => 'Currency List CRUD'],
                        ['label' => 'Currency History', 'url' => ['/currency-history/index'], 'title' => 'Currency History CRUD'],
                    ]
                ],
                [
                    'label' => 'Conferences',
                    'url' => 'javascript:',
                    'icon' => 'folder',
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

                [
                    'label' => 'QCall',
                    'url' => 'javascript:',
                    'icon' => 'folder',
                    'items' => [
                        ['label' => 'Redial user access', 'url' => ['/call-redial-user-access/index']],
                        ['label' => 'Lead QCall List', 'url' => ['/lead-qcall/list']],
                        ['label' => 'Lead QCall All', 'url' => ['/lead-qcall/index']],
                        ['label' => 'QCall Config', 'url' => ['/qcall-config/index']],
                        ['label' => 'Project Weight', 'url' => ['/project-weight/index']],
                        ['label' => 'Status Weight', 'url' => ['/status-weight/index']],
                    ]
                ],
                ['label' => 'Flight Segments', 'url' => ['/lead-flight-segment/index'], 'icon' => 'plane'],
                [
                    'label' => 'Quotes',
                    'url' => 'javascript:',
                    'icon' => 'folder',
                    'items' => [
                        ['label' => 'Quote Communication', 'url' => ['/quote-communication/index'], 'icon' => 'list'],
                        ['label' => 'Quote Communication Open Log', 'url' => ['/quote-communication-open-log/index'], 'icon' => 'list'],
                        ['label' => 'Quote List', 'url' => ['/quotes/index'], 'icon' => 'list'],
                        ['label' => 'Quote Price List', 'url' => ['/quote-price/index'], 'icon' => 'list'],
                        ['label' => 'Flight Quote Label List', 'url' => ['/flight-quote-label-list-crud/index'], 'icon' => 'list'],
                        ['label' => 'Quote Label', 'url' => ['/quote-label-crud/index'], 'icon' => 'list'],
                        ['label' => 'Quote Trip', 'url' => ['/quote-trip-crud/index'], 'icon' => 'list'],
                        ['label' => 'Quote Segment', 'url' => ['/quote-segment-crud/index'], 'icon' => 'list'],
                        ['label' => 'Quote Segment Baggages', 'url' => ['/quote-segment-baggage-crud/index'], 'icon' => 'list'],
                        ['label' => 'Quote Segment Baggage Charges', 'url' => ['/quote-segment-baggage-charge-crud/index'], 'icon' => 'list'],
                        ['label' => 'Quote Segment Stop CRUD', 'url' => ['/quote-segment-stop-crud/index'], 'icon' => 'list'],
                    ],
                ],
                ['label' => 'Call User Access', 'url' => ['/call-user-access/index'], 'icon' => 'list'],
                [
                    'label' => 'Leads',
                    'url' => 'javascript:',
                    'icon' => 'folder',
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
                        ['label' => 'Lead User Data', 'url' => ['/lead-user-data-crud/index']],
                        ['label' => 'Lead User Conversion', 'url' => ['/lead-user-conversion-crud/index']],
                        ['label' => 'Lead Poor Processing Data', 'url' => ['/lead-poor-processing-data-crud/index']],
                        ['label' => 'Lead Poor Processing', 'url' => ['/lead-poor-processing-crud/index']],
                        ['label' => 'Lead Poor Processing Log', 'url' => ['/lead-poor-processing-log-crud/index']],
                        ['label' => 'Lead Business Extra Queue Rules', 'url' => ['/lead-business-extra-queue-rule-crud/index']],
                        ['label' => 'Lead Business Extra Queue', 'url' => ['/lead-business-extra-queue-crud/index']],
                        ['label' => 'Lead Business Extra Queue Log', 'url' => ['/lead-business-extra-queue-log-crud/index']],
                        ['label' => 'Lead User Ratings', 'url' => ['/lead-user-rating-crud/index']],
                        ['label' => 'Lead Status Reason', 'url' => ['/lead-status-reason-crud/index']],
                        ['label' => 'Lead Status Reason Log', 'url' => ['/lead-status-reason-log-crud/index']],
                    ]
                ],
                [
                    'label' => 'Cases',
                    'url' => 'javascript:',
                    'icon' => 'list',
                    'items' => [
                        ['label' => 'Case Event Log', 'url' => ['/case-event-log-crud/index']]
                    ]
                ],
                [
                    'label' => 'Contact Phone numbers',
                    'url' => 'javascript:',
                    'icon' => 'list',
                    'items' => [
                        ['label' => 'Phone List CRUD', 'url' => ['/contact-phone-list-crud/index']],
                        ['label' => 'Phone Data', 'url' => ['/contact-phone-data-crud/index']],
                        ['label' => 'Phone service info', 'url' => ['/contact-phone-service-info-crud/index']],
                        ['label' => 'Phone List', 'url' => ['/contact-phone-list/index']],
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
                ['label' => 'Visitor Subscription', 'url' => ['/visitor-subscription-crud/index'], 'icon' => 'bell'],
                [
                    'label' => 'Notifications',
                    'url' => 'javascript:',
                    'icon' => 'list',
                    'items' => [
                        ['label' => 'Notifications', 'url' => ['/client-notification/index']],
                        ['label' => 'Phone', 'url' => ['/client-notification-phone-list/index']],
                        ['label' => 'Sms', 'url' => ['/client-notification-sms-list/index']],
                    ]
                ],
                [
                    'label' => 'Client Data',
                    'url' => 'javascript:',
                    'icon' => 'th-list',
                    'items' => [
                        ['label' => 'Client Data Key', 'url' => ['/client-data-key-crud/index'], 'icon' => 'key'],
                        ['label' => 'Client Data', 'url' => ['/client-data-crud/index'], 'icon' => 'list'],
                    ]
                ],
                ['label' => 'Client User Return', 'url' => ['/client-user-return-crud/index'], 'icon' => 'user'],
            ],
        ];

        $menuNewData = [
            'label' => 'New Data',
            'url' => 'javascript:',
            'icon' => 'list',
            'items' => [
                ['label' => 'Products', 'url' => 'javascript:', 'items' => [
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
                    ['label' => 'Product Quote Refund', 'url' => ['/product/product-quote-refund-crud/index']],
                    ['label' => 'Product Quote Option Refund', 'url' => ['/product/product-quote-option-refund-crud/index']],
                    ['label' => 'Product Quote Object Refund', 'url' => ['/product/product-quote-object-refund-crud/index']],
                    ['label' => 'Product Quote Change', 'url' => ['/product/product-quote-change-crud/index']],
                    ['label' => 'Product Quote Change Relation', 'url' => ['/product/product-quote-change-relation-crud/index']],
                    ['label' => 'Product Quote Data', 'url' => ['/product/product-quote-data-crud/index']],
                ], 'hasChild' => true],

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
                    ['label' => 'Order Refund', 'url' => ['/order/order-refund-crud/index']],
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
                    'label' => 'Task List',
                    'url' => 'javascript:',
                    'icon' => 'list',
                    'items' => [

                        ['label' => 'Task List', 'url' => ['/task/task-list/index'],
                            //'icon' => '',
                            'title' => 'Task List'
                        ],
                        [
                            'label' => 'User Task',
                            'url' => ['/task/user-task-crud/index'],
                        ],
                        [
                            'label' => 'Object Segment Tasks',
                            'url' => ['/object-segment/object-segment-task-crud/index'],
                        ],
                        [
                            'label' => 'Shift schedule event task',
                            'url' => ['/task/shift-schedule-event-task-crud/index'],
                        ],

                        [
                            'label' => 'Advanced',
                            'url' => 'javascript:',
                            'icon' => 'folder',
                            'items' => [
//                                ['label' => 'Shift Schedule Type', 'url' => ['/shift/shift-schedule-type/index'],
//                                    'title' => 'Shift Schedule Type'],
//
//
//                                ['label' => 'Type Labels Assign',
//                                    'url' => ['/shift/shift-schedule-type-label-assign/index'],
//                                    // 'icon' => 'circle',
//                                    'title' => 'Shift Schedule Type Labels Assign'
//                                ],
                            ]
                        ],


                    ],
                ],

                [
                    'label' => 'Shift Schedules',
                    'url' => 'javascript:',
                    'icon' => 'calendar',
                    'items' => [

                        ['label' => 'Shift List', 'url' => ['/shift-crud/index'], 'title' => 'Shift CRUD'],

                        ['label' => 'Schedule Rule', 'url' => ['/shift-schedule-rule-crud/index'],
                            'title' => 'Shift Schedule Rule'],

                        ['label' => 'Shift Events', 'url' => ['/user-shift-schedule-crud/index'],
                            'title' => 'User Shift Schedule Events'],


                        [
                            'label' => 'Shift Requests History',
                            'url' => ['/shift/shift-schedule-request/index'],
                            'title' => 'User Shift Schedule Request History'
                        ],

                        [
                            'label' => 'Shift Summary Report',
                            'url' => ['/shift-schedule/summary-report'],
                            'title' => 'Shift Summary Report',
                            'visible' => UserShiftScheduleService::shiftSummaryReportIsEnable(),
                        ],

                        /** @abac ShiftAbacObject::ACT_USER_SHIFT_ASSIGN, ShiftAbacObject::ACTION_ACCESS, Access menu UserShiftAssign */
                        [
                            'label' => 'User Shift Schedule Assign',
                            'url' => ['/shift/user-shift-assign/index'],
                            'title' => 'User Shift Assign',
                            'icon' => 'user-plus',
                            'abac'  => [
                                'dto'    => null,
                                'object' => ShiftAbacObject::ACT_USER_SHIFT_ASSIGN,
                                'action' => ShiftAbacObject::ACTION_ACCESS,
                            ],
                        ],

                        [
                            'label' => 'Advanced',
                            'url' => 'javascript:',
                            'icon' => 'folder',
                            'items' => [
                                ['label' => 'Shift Schedule Type', 'url' => ['/shift/shift-schedule-type/index'],
                                    'title' => 'Shift Schedule Type'],

                                ['label' => 'User Shift Assign CRUD', 'url' => ['/user-shift-assign-crud/index'],
                                    'title' => 'Shift Schedule User Assign'],

                                ['label' => 'Shift Category', 'url' => ['/shift-category-crud/index'],
                                    'title' => 'Shift category CRUD'],

                                ['label' => 'Type Labels',
                                    'url' => ['/shift/shift-schedule-type-label/index'],
                                    // 'icon' => 'circle',
                                    'title' => 'Shift Schedule Type Labels'
                                ],
                                ['label' => 'Type Labels Assign',
                                    'url' => ['/shift/shift-schedule-type-label-assign/index'],
                                    // 'icon' => 'circle',
                                    'title' => 'Shift Schedule Type Labels Assign'
                                ],
                                ['label' => 'User Shift Schedule Log',
                                    'url' => ['/shift/user-shift-schedule-log-crud/index'],
                                    // 'icon' => 'circle',
                                    'title' => 'Shift Schedule Type Labels Assign'
                                ],
                            ]
                        ],


                    ],
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
                ['label' => 'Phone BlockList', 'url' => ['/phone-blacklist/index'], 'icon' => 'phone'],
                ['label' => 'Phone BlockList Log', 'url' => ['/phone-blacklist-log-crud/index'], 'icon' => 'phone'],
                ['label' => 'Phone Number Redial', 'url' => ['/phone-number-redial-crud/index'], 'icon' => 'phone'],

                //['label' => 'Airlines', 'url' => ['/settings/airlines'], 'icon' => 'plane'],
                ['label' => 'Airlines', 'url' => ['/airline-crud/index'], 'icon' => 'space-shuttle'],
                ['label' => 'Airports', 'url' => ['/airports/index'], 'icon' => 'plane'],
                ['label' => 'Airport Lang', 'url' => ['/airport-lang-crud/index'], 'icon' => 'language'],

                ['label' => 'ACL (IP)', 'url' => ['/settings/acl'], 'icon' => 'user-secret'],
                ['label' => 'API Users', 'url' => ['/api-user/index'], 'icon' => 'users'],
                ['label' => 'Tasks', 'url' => ['/task/index'], 'icon' => 'list'],
                ['label' => 'Lead Tasks', 'url' => ['/lead-task/index'], 'icon' => 'list'],
                ['label' => 'Profit Split', 'url' => ['/profit-split-crud/index'], 'icon' => 'money'],

                ['label' => 'Check List Types', 'url' => ['/lead-checklist-type/index'], 'icon' => 'list', 'visible' => Yii::$app->user->can('manageLeadChecklistType')],

                [
                    'label' => 'Coupons',
                    'url' => 'javascript:',
                    'icon' => 'ticket',
                    'items' => [
                        ['label' => 'Coupons', 'url' => ['/coupon/index'], 'icon' => 'list'],
                        ['label' => 'Coupon Use', 'url' => ['/coupon-use-crud/index'], 'icon' => 'list'],
                        ['label' => 'Coupon Client', 'url' => ['/coupon-client-crud/index'], 'icon' => 'list'],
                        ['label' => 'Coupon Send', 'url' => ['/coupon-send-crud/index'], 'icon' => 'list'],
                        ['label' => 'Coupon Product', 'url' => ['/coupon-product-crud/index'], 'icon' => 'list'],
                        ['label' => 'Coupon User Action', 'url' => ['/coupon-user-action-crud/index'], 'icon' => 'list'],
                    ],
                ],
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


            ]
        ];

        $userMenuItems = [
            'label' => 'User Addition',
            'url' => 'javascript:',
            'icon' => 'folder',
            'items' => [
                ['label' => 'User Monitor', 'url' => ['/user-monitor/index'], 'icon' => 'list'],
                ['label' => 'User Monitor Stats', 'url' => ['/user-monitor/stats'], 'icon' => 'list'],
                [
                    'label' => 'User model setting crud',
                    'url' => ['/user-model-setting-crud/index'],
                    'icon' => 'list',
                ],
                ['label' => 'User Groups Assignments', 'url' => ['/user-group-assign/index'], 'icon' => 'list'],
                ['label' => 'User Commission Rules', 'url' => ['/user-commission-rules-crud/index'], 'icon' => 'list'],
                ['label' => 'User Bonus Rules', 'url' => ['/user-bonus-rules-crud/index'], 'icon' => 'list'],
                ['label' => 'User Failed Login', 'url' => ['/user-failed-login/index'], 'icon' => 'list'],

                ['label' => 'User Stat Day', 'url' => ['/user-stat-day-crud/index'], 'icon' => 'list'],
                ['label' => 'User Data', 'url' => ['/user-data-crud/index'], 'icon' => 'list'],
                ['label' => 'User Auth Client', 'url' => ['/auth-client-crud/index'], 'icon' => 'list'],
                ['label' => 'User Feedback', 'url' => ['/user-feedback-crud/index'], 'icon' => 'comments',
                    'title' => 'User Feedback CRUD'],
                ['label' => 'User Feedback Files', 'url' => ['/user-feedback-file-crud/index'], 'icon' => 'list',
                    'title' => 'User Feedback Files CRUD'],
                [
                    'label' => 'User Product Type',
                    'url' => ['/user-product-type/index'],
                    'icon' => 'list',
                    'visible' => Yii::$app->user->can('user-product-type/list')
                ],
            ]
        ];

        $menuItems[] = [
            'label' => 'Users',
            'url' => 'javascript:',
            'icon' => 'user',
            'items' => [
                [
                    /** @abac $userFeedbackAbacDto, UserFeedbackAbacObject::ACT_USER_FEEDBACK_INDEX, UserFeedbackAbacObject::ACTION_ACCESS, Access to view list of  User Feedback*/
                    'label' => 'My Feedbacks',
                    'url'   => ['/user-feedback/index'],
                    'icon'  => 'newspaper-o',
                    'abac'  => [
                        'dto'    => new UserFeedbackAbacDto(),
                        'object' => UserFeedbackAbacObject::ACT_USER_FEEDBACK_INDEX,
                        'action' => UserFeedbackAbacObject::ACTION_ACCESS
                    ],
                ],
                ['label' => 'Users', 'url' => ['/employee/list'], 'icon' => 'users'],
                ['label' => 'User Online', 'url' => ['/user-online/index'], 'icon' => 'spinner'],
                ['label' => 'User Connections', 'url' => ['/user-connection/index'], 'icon' => 'plug'],
                ['label' => 'User Groups', 'url' => ['/user-group/index'], 'icon' => 'users'],
                ['label' => 'User Groups Set', 'url' => ['/user-group-set/index'], 'icon' => 'users'],
                ['label' => 'User Params', 'url' => ['/user-params/index'], 'icon' => 'bars'],
                ['label' => 'User Project Params', 'url' => ['/user-project-params/index'], 'icon' => 'list'],
                ['label' => 'User Status', 'url' => ['/user-status/index'], 'icon' => 'sliders'],
                $userMenuItems
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

        if (class_exists('\modules\experiment\ExperimentModule')) {
            $menuModuleItems[] = [
                'label' => 'Experiments',
                'url' => 'javascript:',
                'icon' => 'flask',
                'items' => \modules\experiment\ExperimentModule::getListMenu()
            ];
        }


        /** @fflag FFlag::FF_KEY_OBJECT_SEGMENT_MODULE_ENABLE, Object Segment module enable/disable */
        if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_OBJECT_SEGMENT_MODULE_ENABLE)) {
            $menuModuleItems[] =  [
                'label' => 'Object Segment',
                'url' => 'javascript:',
                'icon' => 'cogs',
                'items' => [
                    ['label' => 'Object Segment List', 'url' => ['/object-segment/object-segment-list/index']],
                    ['label' => 'Object Segment Rules', 'url' => ['/object-segment/object-segment-rule/index']],
                ],
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

        $menuItems[] = ['label' => 'My Sales', 'url' => ['/sales/index'], 'icon' => 'money'];

        $menuHeatMapItems[] = [
            'label' => 'Heat Map Leads',
            'url' => ['/heat-map-lead/index'],
            'icon' => 'area-chart',
            'abac' => [
                'dto' => new LeadAbacDto(null, (int) Auth::id()),
                'object' => LeadAbacObject::OBJ_HEAT_MAP_LEAD,
                'action' => LeadAbacObject::ACTION_ACCESS
            ],
        ];
        /** @fflag FFlag::FF_KEY_HEAT_MAP_AGENT_REPORT_ENABLE, Heat Map Agent Report enable\disable */
        if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_HEAT_MAP_AGENT_REPORT_ENABLE)) {
            $menuHeatMapItems[] =  [
                'label' => 'Heat Map Agent',
                'url' => ['/heat-map-agent/index'],
                'icon' => 'area-chart'
            ];
        }

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
                ['label' => 'User Stats Report', 'url' => ['/user-stats/report'], 'icon' => 'users'],
                ['label' => 'User Feedback Statistics', 'url' => ['/stats/user-feedback'], 'icon' => 'users'],
                /** @abac $leadAbacDto, LeadAbacObject::OBJ_HEAT_MAP_LEAD, LeadAbacObject::ACTION_ACCESS, show heat-map-lead in menu */
                [
                    'label' => 'Heat Map',
                    'url' => 'javascript:',
                    'icon' => 'folder',
                    'items' => $menuHeatMapItems
                ],
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
                ['label' => 'Site Settings', 'url' => ['/setting/index'], 'icon' => 'cogs text-success'],
                ['label' => 'System Logs', 'url' => ['/log/index'], 'icon' => 'bug text-warning'],
                ['label' => 'API Logs', 'url' => ['/api-log/index'], 'icon' => 'sitemap'],
                [
                    'label' => Yii::t('language', 'Feature Flag'), 'url' => 'javascript:', 'icon' => 'flag',
                    'items' => [
                        ['label' => Yii::t('menu', 'Feature Flag'), 'url' => ['/flag/feature-flag/index'],
                            'title' => 'Feature Flag CRUD'],
                        ['label' => Yii::t('menu', 'Feature Flag Values'), 'url' => ['/flag/feature-flag-value/index'],
                            'title' => 'Feature Flag Values'],
                        ['label' => Yii::t('menu', 'Feature Flag Experiment'), 'url' => ['/flag/feature-flag-experiment/index'],
                            'title' => 'Feature Flag Experiment'],
                        ['label' => Yii::t('menu', 'Feature Flag Docs'), 'url' => ['/flag/feature-flag/doc'],
                            'title' => 'Feature Flag Docs'],
                        ['label' => Yii::t('menu', 'Feature Flag Test'), 'url' => ['/flag/feature-flag/test'],
                            'title' => 'Feature Flag Test'],
                    ]
                ],
                [
                    'label' => Yii::t('language', 'Events'), 'url' => 'javascript:', 'icon' => 'folder',
                    'items' => [
                        ['label' => Yii::t('menu', 'Event List'), 'url' => ['/event-list/index'],
                            'title' => 'Event List CRUD'],
                        ['label' => Yii::t('menu', 'Event Handler'), 'url' => ['/event-handler/index'],
                            'title' => 'Event Handler CRUD'],
                    ]
                ],
                ['label' => 'API Report', 'url' => ['/stats/api-graph'], 'icon' => 'bar-chart'],

                [
                    'label' => Yii::t('requestControl', 'Request Control'), 'url' => 'javascript:', 'icon' => 'folder',
                    'items' => [
                        ['label' => 'User Site Activity', 'url' => ['/request-control/user-site-activity/index'], 'icon' => 'bars'],
                        ['label' => 'User Activity Report', 'url' => ['/request-control/user-site-activity/report'], 'icon' => 'bar-chart'],
                        ['label' => 'Request Control Manage', 'url' => ['/request-control/manage/index'], 'icon' => 'bars']
                    ]
                ],
                ['label' => 'Global Model Logs', 'url' => ['/global-log/index'], 'icon' => 'list'],
                ['label' => 'Clean cache & assets', 'url' => ['/clean/index'], 'icon' => 'remove'],
                [
                    'label' => Yii::t('language', 'Tools'), 'url' => 'javascript:', 'icon' => 'cog',
                    'items' => [
                        ['label' => Yii::t('menu', 'Check Flight Dump'), 'url' => ['/tools/check-flight-dump']],
                        ['label' => Yii::t('menu', 'Check Exclude IP'), 'url' => ['/tools/check-exclude-ip']],
                        ['label' => Yii::t('menu', 'Stash Log Files'), 'url' => ['/tools/stash-log-file']],
                        ['label' => Yii::t('menu', 'DB Info'), 'url' => ['/tools/db-info']],
                        ['label' => Yii::t('menu', 'Db Data Sensitive'), 'url' => ['/db-data-sensitive-crud/index'], 'icon' => 'database'],
                        ['label' => Yii::t('menu', 'Composer Info'), 'url' => ['/tools/composer-info']],
                        ['label' => 'Check phone', 'url' => ['/tools/check-phone'], 'icon' => 'volume-control-phone'],
                        ['label' => 'Import phones', 'url' => ['/tools/import-phone'], 'icon' => 'caret-square-o-up'],
                    ]
                ],

                $menuLanguages,

                ['label' => 'Site Settings Category', 'url' => ['/setting-category/index'], 'icon' => 'list'],

                ['label' => 'Virtual cron', 'url' => ['/virtual-cron/cron-scheduler/index'], 'icon' => 'cogs'],
                ['label' => 'Site ENV', 'url' => ['/setting/env'], 'icon' => 'info-circle'],
                ['label' => 'Call Terminate Log', 'url' => ['/call-terminate-log-crud/index'], 'icon' => 'list'],
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
                ['label' => 'RBAC Role Management', 'url' => ['/rbac-role-management/index/']],
                ['label' => 'Import / Export', 'url' => ['/rbac-import-export/log']],
            ],
        ];

        $menuItems[] = [
            'label' => 'ABAC',
            'url' => 'javascript:',
            'icon' => 'cogs',
            'items' => [
                ['label' => 'ABAC Policy List', 'url' => ['/abac/abac-policy/index']],
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

            $searchValue = $item['label'];
            if (!empty($item['title'])) {
                $searchValue .= ' ' . $item['title'];
            }

            if (stripos(strip_tags($searchValue), $text) === false) {
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
            if (isset($item['abac'])) {
                try {
                    $abacItem = (array) $item['abac'];
                    if (!array_key_exists('dto', $abacItem)) {
                        throw new \RuntimeException('Abac dto is empty');
                    }

                    $abacDto = $item['abac']['dto'] ?? null;
                    if (!$object = $item['abac']['object'] ?? null) {
                        throw new \RuntimeException('Abac object is empty');
                    }
                    if (!$action = $item['abac']['action'] ?? null) {
                        throw new \RuntimeException('Abac action is empty');
                    }
                    $item['visible'] = (bool) Yii::$app->abac->can($abacDto, $object, $action);
                } catch (\RuntimeException | \DomainException $throwable) {
                    $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $item);
                    \Yii::warning($message, 'SideBarMenu:ensureVisibility:Exception');
                } catch (\Throwable $throwable) {
                    $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $item);
                    \Yii::error($message, 'SideBarMenu:ensureVisibility:Throwable');
                }
            }
            if (isset($item['label']) && (!isset($item['visible']) || $item['visible'] === true)) {
                $allVisible = true;
            }
        }
        return $allVisible;
    }
}
