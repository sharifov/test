<?php

use \yii\helpers\Url;

/* @var $this \yii\web\View */
/** @var \common\models\Employee $user */

$user = Yii::$app->user->identity;

$isAdmin = $user->canRole('admin') || $user->canRole('superadmin');
$isSupervision = $user->canRole('supervision');
$isAgent = $user->canRole('agent');
$isQA = $user->canRole('qa');
$isUM = $user->canRole('userManager');
$isSuperAdmin = $user->canRole('superadmin');

?>
<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
        <br>
        <br>
        <br>
        <?php

        $menuItems = [];

        $menuItems[] = ['label' => 'Create new Lead', 'url' => ['/lead/create'], 'icon' => 'plus'];
        $menuItems[] = ['label' => 'Create new Case', 'url' => ['/cases/create'], 'icon' => 'plus'];

        if ($user->canCall()) {
            $menuItems[] = ['label' => 'Auto redial', 'url' => ['/call/auto-redial'], 'icon' => 'tty'];
        }

        $menuItems[] = ['label' => 'Dashboard', 'url' => ['/'], 'icon' => 'area-chart'];
        $menuItems[] = ['label' => 'Search Leads', 'url' => ['/leads/index'], 'icon' => 'search'];
        $menuItems[] = ['label' => 'Search Sale', 'url' => ['/sale/search'], 'icon' => 'search'];
        $menuItems[] = ['label' => 'Search Cases', 'url' => ['/cases'], 'icon' => 'search'];

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
            'label' => 'Stats',
            'url' => 'javascript:',
            'icon' => 'bar-chart',
            'items' => [
                ['label' => 'Agents report', 'url' => ['/agent-report/index'], 'icon' => 'users'],
                ['label' => 'Calls & SMS', 'url' => ['/stats/call-sms'], 'icon' => 'phone'],
                ['label' => 'Calls Report', 'url' => ['/stats/calls-graph'], 'icon' => 'line-chart'],
                ['label' => 'SMS Report', 'url' => ['/stats/sms-graph'], 'icon' => 'line-chart'],
                ['label' => 'Emails Report', 'url' => ['/stats/emails-graph'], 'icon' => 'line-chart'],
                ['label' => 'Stats Employees', 'url' => ['/stats/index'], 'icon' => 'users'],
                ['label' => 'User Connections', 'url' => ['/user-connection/index'], 'icon' => 'plug'],
                ['label' => 'User Stats', 'url' => ['/user-connection/stats'], 'icon' => 'area-chart'],
                ['label' => 'Call User Map', 'url' => ['/call/user-map'], 'icon' => 'map'],
                ['label' => 'Call List', 'url' => ['/call/index'], 'icon' => 'phone'],
                ['label' => 'SMS List', 'url' => ['/sms/index'], 'icon' => 'comments-o'],
                ['label' => 'Mail List', 'url' => ['/email/index'], 'icon' => 'envelope'],
            ]
        ];

        $menuItems[] = [
            'label' => 'Additional',
            'url' => 'javascript:',
            'icon' => 'list',
            'items' => [
                ['label' => 'All Notifications', 'url' => ['/notifications/index'], 'icon' => 'comment-o'],
                ['label' => 'User Call Statuses', 'url' => ['/user-call-status/index'], 'icon' => 'list'],
                ['label' => 'Lead Call Experts', 'url' => ['/lead-call-expert/index'], 'icon' => 'bell'],
                ['label' => 'Flight Segments', 'url' => ['/lead-flight-segment/index'], 'icon' => 'plane'],
                ['label' => 'Quote List', 'url' => ['/quotes/index'], 'icon' => 'quora'],
                ['label' => 'Quote Price List', 'url' => ['/quote-price/index'], 'icon' => 'dollar'],
                ['label' => 'Export Leads', 'url' => ['/leads/export'], 'icon' => 'export'],
                ['label' => 'Duplicate Leads', 'url' => ['/leads/duplicate'], 'icon' => 'copy'],
                ['label' => 'Stats Agents & Leads', 'url' => ['/report/agents'], 'icon' => 'users'],
                ['label' => 'Lead Status History', 'url' => ['/lead-flow/index'], 'icon' => 'list'],
                ['label' => 'Lead Check Lists', 'url' => ['/lead-checklist/index'], 'icon' => 'list', 'visible' => Yii::$app->user->can('manageLeadChecklist')],
                ['label' => 'LF Checklist Status History', 'url' => ['/lead-flow-checklist/index'], 'icon' => 'list', 'visible' => Yii::$app->user->can('viewLeadFlowChecklist')],
            ]
        ];

        if ($isAdmin || $user->isKpiEnable()) {
            $menuItems[] = ['label' => 'KPI <span id="kpi" class="label-info label pull-right"></span> ', 'url' => ['/kpi/index'], 'icon' => 'money'];
        }

        $menuLItems = [];
        $menuLItems[] = ['label' => 'Pending <span id="badges-pending" data-type="pending" class="label-info label pull-right bginfo"></span> ', 'url' => ['/queue/pending'], 'icon' => 'briefcase text-info'];

        if (isset(Yii::$app->params['settings']['enable_lead_inbox']) && Yii::$app->params['settings']['enable_lead_inbox']) {
            $menuLItems[] = ['label' => 'Inbox <span id="badges-inbox" data-type="inbox" class="label-info label pull-right bginfo"></span> ', 'url' => ['/queue/inbox'], 'icon' => 'briefcase text-info'];
        }

        $menuLItems[] = ['label' => 'Follow Up <span id="badges-follow-up" data-type="follow-up" class="label-success label pull-right bginfo"></span> ', 'url' => ['/queue/follow-up'], 'icon' => 'recycle'];
        $menuLItems[] = ['label' => 'Processing <span id="badges-processing" data-type="processing" class="label-warning label pull-right bginfo"></span> ', 'url' => ['/queue/processing'], 'icon' => 'spinner'];
        $menuLItems[] = ['label' => 'Booked <span id="badges-booked" data-type="booked" class="label-success label pull-right bginfo"></span>', 'url' => ['/queue/booked'], 'icon' => 'flag-o text-warning'];
        $menuLItems[] = ['label' => 'Sold <span id="badges-sold" data-type="sold" class="label-success label pull-right bginfo"></span> ', 'url' => ['/queue/sold'], 'icon' => 'flag text-success'];
        $menuLItems[] = ['label' => 'Duplicate <span id="badges-duplicate" data-type="duplicate" class="label-danger label pull-right bginfo"></span>', 'url' => ['/queue/duplicate'], 'icon' => 'list text-danger'];
        $menuLItems[] = ['label' => 'Trash <span id="badges-trash" class="label-danger label pull-right"></span>', 'url' => ['/queue/trash'], 'icon' => 'trash-o text-danger'];


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
        $menuCases[] = ['label' => 'Pending <span id="cases-q-pending" data-type="pending" class="label-warning label pull-right cases-q-info"></span> ', 'url' => ['/cases-q/pending'], 'icon' => 'briefcase text-info'];
        $menuCases[] = ['label' => 'Inbox <span id="cases-q-inbox" data-type="inbox" class="label-warning label pull-right cases-q-info"></span> ', 'url' => ['/cases-q/inbox'], 'icon' => 'briefcase text-info'];
        $menuCases[] = ['label' => 'Processing <span id="cases-q-processing" data-type="processing" class="label-warning label pull-right cases-q-info"></span> ', 'url' => ['/cases-q/processing'], 'icon' => 'spinner'];
        $menuCases[] = ['label' => 'Follow Up <span id="cases-q-followup" data-type="followup" class="label-success label pull-right cases-q-info"></span> ', 'url' => ['/cases-q/followup'], 'icon' => 'recycle'];
        $menuCases[] = ['label' => 'Solved <span id="cases-q-solved" data-type="solved" class="label-success label pull-right cases-q-info"></span> ', 'url' => ['/cases-q/solved'], 'icon' => 'flag text-success'];
        $menuCases[] = ['label' => 'Trash <span id="cases-q-trash" class="label-danger label pull-right"></span>', 'url' => ['/cases-q/trash'], 'icon' => 'trash-o text-danger'];

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

        $menuItems[] = [
            'label' => 'Users',
            'url' => 'javascript:',
            'icon' => 'user',
            'items' => [
                ['label' => 'Users', 'url' => ['/employee/list'], 'icon' => 'user'],
                ['label' => 'User Groups', 'url' => ['/user-group/index'], 'icon' => 'users'],
                ['label' => 'User Params', 'url' => ['/user-params/index'], 'icon' => 'bars'],
                ['label' => 'User Project Params', 'url' => ['/user-project-params/index'], 'icon' => 'list'],
                ['label' => 'User Groups Assignments', 'url' => ['/user-group-assign/index'], 'icon' => 'list'],

            ]
        ];

        $menuItems[] = [
            'label' => 'Clients',
            'url' => 'javascript:',
            'icon' => 'users',
            'items' => [
                ['label' => 'Clients', 'url' => ['/client/index'], 'icon' => 'users'],
                ['label' => 'Clients phones', 'url' => ['/client-phone/index'], 'icon' => 'phone'],
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
                ['label' => 'Clean cache & assets', 'url' => ['/clean/index'], 'icon' => 'remove'],
                ['label' => 'Site Settings', 'url' => ['/setting/index'], 'icon' => 'cogs'],
                ['label' => 'User Site Activity', 'url' => ['/user-site-activity/index'], 'icon' => 'bars'],
                ['label' => 'User Activity Report', 'url' => ['/user-site-activity/report'], 'icon' => 'bar-chart'],
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

        echo \yiister\gentelella\widgets\Menu::widget([
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
$urlBadgesCount = Url::to(['/badges/get-badges-count']);
$js =<<<JS
function updateBadgesCounters() {
    $(".bginfo").each(function(i) {
        var id = $(this).attr('id');
        var type = $(this).data('type');
        if (typeof(type) != "undefined" && type != null && typeof(id) != "undefined" && id != null ) {
              setTimeout(function () {
                  $('#' + id).html('<i style="font-size:9px" class="fa fa-spin fa-spinner"></i>');
              $.ajax({
                url: '$urlBadgesCount',
                data: {type: type}, 
                dataType: 'json',
                success: function(data){
                    if (typeof (data) != "undefined" && data != null) {
                        if (data.result == 'success' && data.count != 0) {
                            $("#" + id).html(data.count);
                        } else {
                            $("#" + id).html('');
                        }
                    } else {
                        $("#" + id).html('-');
                    }
         
                },
                error: function(data){
                    console.log(data);
                    $("#" + id).html('-');
                }, 
            });    
              }, i*300)
        }
    });
}

updateBadgesCounters();
JS;
$this->registerJs($js, $this::POS_LOAD);

$urlCasesQCount = Url::to(['/cases-q-counters/get-q-count']);
$js =<<<JS
function updateCasesQCounters() {
    $(".cases-q-info").each(function(i) {
        var id = $(this).attr('id');
        var type = $(this).data('type');
        if (typeof(type) != "undefined" && type != null && typeof(id) != "undefined" && id != null ) {
              setTimeout(function () {
                  $('#' + id).html('<i style="font-size:9px" class="fa fa-spin fa-spinner"></i>');
              $.ajax({
                url: '$urlCasesQCount',
                data: {type: type}, 
                dataType: 'json',
                success: function(data){
                    if (typeof (data) != "undefined" && data != null) {
                        if (data.result == 'success' && data.count != 0) {
                            $("#" + id).html(data.count);
                        } else {
                            $("#" + id).html('');
                        }
                    } else {
                        $("#" + id).html('-');
                    }
         
                },
                error: function(data){
                    console.log(data);
                    $("#" + id).html('-');
                }, 
            });    
              }, i*300)
        }
    });
}

updateCasesQCounters();
JS;
$this->registerJs($js, $this::POS_LOAD);
