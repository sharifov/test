<?php

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




        if ($user->canCall()) {
            $menuItems[] = ['label' => 'Auto redial', 'url' => ['/call/auto-redial'], 'icon' => 'tty'];
        }

        $menuItems[] = ['label' => 'Dashboard', 'url' => ['/dashboard/index'], 'icon' => 'area-chart'];




        $menuLItems = [];

        $menuLItems[] = ['label' => 'Create new Lead', 'url' => ['/lead/create'], 'icon' => 'plus'];
        $menuLItems[] = ['label' => 'Search Leads', 'url' => ['/leads/index'], 'icon' => 'search'];
        $menuLItems[] = ['label' => 'Pending <span id="badges-pending" data-type="pending" class="label-info label pull-right bginfo"></span> ', 'url' => ['/queue/pending'], 'icon' => 'briefcase text-info'];

        if (isset(Yii::$app->params['settings']['enable_lead_inbox']) && Yii::$app->params['settings']['enable_lead_inbox']) {
            $menuLItems[] = ['label' => 'Inbox <span id="badges-inbox" data-type="inbox" class="label-info label pull-right bginfo"></span> ', 'url' => ['/queue/inbox'], 'icon' => 'briefcase text-info'];
        }

        if (($profile = $user->userProfile) && $profile->up_auto_redial) {
            $menuLItems[] = ['label' => 'Lead Redial <span id="badges-redial" data-type="redial" class="label-info label pull-right bginfo"></span>', 'url' => ['/lead-redial/index'], 'icon' => 'phone'];
        }

        $menuLItems[] = ['label' => 'Lead Follow Up <span id="badges-follow-up" data-type="follow-up" class="label-success label pull-right bginfo"></span> ', 'url' => ['/queue/follow-up'], 'icon' => 'recycle'];
        $menuLItems[] = ['label' => 'Lead Processing <span id="badges-processing" data-type="processing" class="label-warning label pull-right bginfo"></span> ', 'url' => ['/queue/processing'], 'icon' => 'spinner'];
        $menuLItems[] = ['label' => 'Lead Booked <span id="badges-booked" data-type="booked" class="label-success label pull-right bginfo"></span>', 'url' => ['/queue/booked'], 'icon' => 'flag-o text-warning'];
        $menuLItems[] = ['label' => 'Lead Sold <span id="badges-sold" data-type="sold" class="label-success label pull-right bginfo"></span> ', 'url' => ['/queue/sold'], 'icon' => 'flag text-success'];
        $menuLItems[] = ['label' => 'Lead Duplicate <span id="badges-duplicate" data-type="duplicate" class="label-danger label pull-right bginfo"></span>', 'url' => ['/queue/duplicate'], 'icon' => 'list text-danger'];
        $menuLItems[] = ['label' => 'Lead Trash <span id="badges-trash" class="label-danger label pull-right"></span>', 'url' => ['/queue/trash'], 'icon' => 'trash-o text-danger'];


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
            $cntNotifications = \common\models\Notifications::findNewCount(Yii::$app->user->id);
            $menuItems[] = [
                'label' => 'My Notifications' .
                    '<span id="div-cnt-notification">' . ($cntNotifications ? '<span class="label-success label pull-right">' . $cntNotifications . '</span>' : '') . '</span>',
                'url' => ['/notifications/list'],
                'icon' => 'comment',
            ];
        }

        $menuItems[] = ['label' => 'My Mails <span id="email-inbox-queue" class="label-info label pull-right"></span> ', 'url' => ['/email/inbox'], 'icon' => 'envelope'];

        $smsExist = \common\models\UserProjectParams::find()
            ->where(['upp_user_id' => Yii::$app->user->id])
            ->andWhere([
                'AND', ['IS NOT', 'upp_tw_phone_number', null],
                ['<>', 'upp_tw_phone_number', '']
            ])
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
                ['label' => 'SMS List', 'url' => ['/sms/index'], 'icon' => 'comments-o'],
                ['label' => 'Mail List', 'url' => ['/email/index'], 'icon' => 'envelope'],
                ['label' => 'Notification List', 'url' => ['/notifications/index'], 'icon' => 'comment-o'],
                ['label' => 'Conference Room', 'url' => ['/conference-room/index'], 'icon' => 'comment'],
                ['label' => 'Conferences', 'url' => ['/conference/index'], 'icon' => 'comment'],
                ['label' => 'Conference Participant', 'url' => ['/conference-participant/index'], 'icon' => 'phone'],
            ]
        ];

        $menuItems[] = [
            'label' => 'Stats & Reports',
            'url' => 'javascript:',
            'icon' => 'bar-chart',
            'items' => [
                ['label' => 'Agents report', 'url' => ['/agent-report/index'], 'icon' => 'users'],
                ['label' => 'Calls & SMS', 'url' => ['/stats/call-sms'], 'icon' => 'phone'],
                ['label' => 'Calls report', 'url' => ['/report/calls-report'], 'icon' => 'table'],
				['label' => 'Leads report', 'url' => ['/report/leads-report'], 'icon' => 'table'],
				['label' => 'Calls Stats', 'url' => ['/stats/calls-graph'], 'icon' => 'line-chart'],
                ['label' => 'SMS Stats', 'url' => ['/stats/sms-graph'], 'icon' => 'line-chart'],
                ['label' => 'Emails Stats', 'url' => ['/stats/emails-graph'], 'icon' => 'line-chart'],
                ['label' => 'Stats Employees', 'url' => ['/stats/index'], 'icon' => 'users'],
                ['label' => 'User Connections', 'url' => ['/user-connection/index'], 'icon' => 'plug'],
                ['label' => 'User Stats', 'url' => ['/user-connection/stats'], 'icon' => 'area-chart'],
                ['label' => 'Call User Map', 'url' => ['/call/user-map'], 'icon' => 'map'],
                ['label' => 'Agents Ratings', 'url' => ['/stats/agent-ratings'], 'icon' => 'star-half-empty'],
            ]
        ];

        $menuItems[] = [
            'label' => 'QCall',
            'url' => 'javascript:',
            'icon' => 'list',
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
            ]
        ];

        $menuItems[] = [
            'label' => 'Data Settings',
            'url' => 'javascript:',
            'icon' => 'list',
            'items' => [
                ['label' => 'Projects', 'url' => ['/project/index'], 'icon' => 'product-hunt'],
                ['label' => 'Project Sources', 'url' => ['/sources/index'], 'icon' => 'product-hunt'],
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
                ['label' => 'Cases status history', 'url' => ['/cases-status-log'], 'icon' => 'bars'],
                ['label' => 'Cases categories', 'url' => ['/cases-category'], 'icon' => 'users'],
            ]
        ];

        if (class_exists('\modules\flight\FlightModule')) {
            $menuItems[] = [
                'label' => 'Flight module',
                'url' => 'javascript:',
                'icon' => 'plane',
                'items' => \modules\flight\FlightModule::getListMenu()
            ];
        }

        if (class_exists('\modules\hotel\HotelModule')) {
            $menuItems[] = [
                'label' => 'Hotel module',
                'url' => 'javascript:',
                'icon' => 'hotel',
                'items' => \modules\hotel\HotelModule::getListMenu()
            ];
        }

        $menuItems[] = [
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
                ['label' => 'Orders', 'url' => ['/order/order-crud/index']],
                ['label' => 'Orders Status Log', 'url' => ['/order/order-status-log-crud/index']],
                ['label' => 'Offers', 'url' => ['/offer/offer-crud/index']],
                ['label' => 'Offers Send Log', 'url' => ['/offer/offer-send-log-crud/index']],
                ['label' => 'Offers View Log', 'url' => ['/offer/offer-view-log-crud/index']],
                ['label' => 'Offers Status Log', 'url' => ['/offer/offer-status-log-crud/index']],
                ['label' => 'Offer Products', 'url' => ['/offer/offer-product-crud/index']],
                ['label' => 'Order Products', 'url' => ['/order/order-product-crud/index']],
                ['label' => 'Invoices', 'url' => ['/invoice/invoice-crud/index']],
                ['label' => 'Invoices Status Log', 'url' => ['/invoice/invoice-status-log-crud/index']],
                ['label' => 'Billing Info', 'url' => ['/billing-info/index']],
                ['label' => 'Credit Cards', 'url' => ['/credit-card/index']],
                ['label' => 'Payments', 'url' => ['/payment/index']],
                ['label' => 'Payment Methods', 'url' => ['/payment-method/index']],
                ['label' => 'Transactions', 'url' => ['/transaction/index']],
            ]
        ];

        $menuItems[] = [
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
                ['label' => 'API Logs', 'url' => ['/api-log/index'], 'icon' => 'sitemap'],
                ['label' => 'API Report', 'url' => ['/stats/api-graph'], 'icon' => 'bar-chart'],
                ['label' => 'System Logs', 'url' => ['/log/index'], 'icon' => 'bars'],
                ['label' => 'Action Logs', 'url' => ['/log/action'], 'icon' => 'bars'],
                ['label' => 'Clean cache & assets', 'url' => ['/clean/index'], 'icon' => 'remove'],
                ['label' => 'Site Settings', 'url' => ['/setting/index'], 'icon' => 'cogs'],
                ['label' => 'Site Settings Category', 'url' => ['/setting-category/index'], 'icon' => 'cogs'],
                ['label' => 'User Site Activity', 'url' => ['/user-site-activity/index'], 'icon' => 'bars'],
                ['label' => 'User Activity Report', 'url' => ['/user-site-activity/report'], 'icon' => 'bar-chart'],
				['label' => 'Global Model Logs', 'url' => ['/global-log/index'], 'icon' => 'list'],
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
            ],
        ];

        ensureVisibility($menuItems);

        echo \frontend\themes\gentelella_v2\widgets\Menu::widget([
            'items' => $menuItems,
            'encodeLabels' => false,
            'activateParents' => true,
            'linkTemplate' => '<a href="{url}">{icon}<span>{label}</span>{badge}</a>'
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
