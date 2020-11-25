<?php

use common\models\Employee;
use sales\model\user\entity\monitor\UserMonitor;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $data yii\data\ActiveDataProvider */
/* @var $searchModel \sales\model\user\entity\monitor\search\UserMonitorSearch */
/* @var $startDateTime string */
/* @var $endDateTime string */

$bundle = \frontend\assets\Timeline2Asset::register($this);

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);

/** @var Employee $user */
$user = Yii::$app->user->identity;
?>
<div id="myTimeline">
    <ul class="timeline-events">
        <?php if (!empty($data['items'])) : ?>
            <?php /** @var UserMonitor $item */
            foreach ($data['items'] as $item) :
                if (!array_key_exists($item->um_user_id, $data['users'])) {
                    continue;
                }

                $tlData = [];
                $tlData['id'] = $item->um_id;
                $tlData['row'] = $data['user2row'][$item->um_user_id];

                $tlData['extend'] = [
                    'toggle' => 'popover',
                    'trigger' => 'hover',
                    //'html' => false
                ];

                $tlData['start'] = \common\models\Employee::convertTimeFromUtcToUserTime($user->getTimezone(), strtotime($item->um_start_dt));
                if ($item->um_end_dt) {
                    $tlData['end'] = \common\models\Employee::convertTimeFromUtcToUserTime($user->getTimezone(), strtotime($item->um_end_dt)); //$item->um_end_dt;
                } else {
                    $tlData['size'] = 'small';
                }

                $tlData['bgColor'] = $item->getTypeBgColor();

                if ($item->um_type_id === UserMonitor::TYPE_LOGIN) {
                    $tlData['height'] = 40;
                    if (!$item->um_end_dt) {
                        $tlData['end'] = \common\models\Employee::convertTimeFromUtcToUserTime($user->getTimezone(), strtotime($item->um_start_dt) + 90);
                    }

                    $tlData['content'] = $item->getTypeName() . ' ' . Yii::$app->formatter->asDatetime(strtotime($tlData['start']));
                }

                if ($item->um_type_id === UserMonitor::TYPE_LOGOUT) {
                    $tlData['content'] = $item->getTypeName() . ' ' . Yii::$app->formatter->asDatetime(strtotime($tlData['start']));
                }

                if ($item->um_type_id === UserMonitor::TYPE_ACTIVE) {
                    $tlData['height'] = 32;
                    if (!$item->um_end_dt) {
                        $tlData['end'] = \common\models\Employee::convertTimeFromUtcToUserTime($user->getTimezone(), time());
                    }

                    $totalDuration = strtotime($tlData['end']) - strtotime($tlData['start']);

                    if ($totalDuration < 0) {
                        $totalDuration = 1;
                    }

                    $tlData['content'] = $item->getTypeName() . ' ' . gmdate('H:i:s', $totalDuration);
                }

                if ($item->um_type_id === UserMonitor::TYPE_ONLINE) {
                    if (!$item->um_end_dt) {
                        $tlData['end'] = \common\models\Employee::convertTimeFromUtcToUserTime($user->getTimezone(), time());
                    }
                }

                $title = $item->um_id; //getTypeName()
                ?>
                <li data-timeline-node='<?= json_encode($tlData, JSON_THROW_ON_ERROR) ?>'></li>
            <?php endforeach; ?>

        <?php endif; ?>
    </ul>
</div>

<!-- Timeline Event Detail View Area (optional) -->
<div class="timeline-event-view" style="color: #f8e7ab"></div>

<?php
$userList = [];

if (!empty($data['users'])) {
    foreach ($data['users'] as $userId => $username) {
        $userList[] = '\'<div style="margin: 0 10px 0 10px"><i class="fa fa-user"></i> ' . Html::encode($username) . ' (' . $userId . ') </div>\'';
    }
}

$userListStr = implode(', ', $userList);

if (Yii::$app->user->identity->timezone) {
    $timeZone = Yii::$app->user->identity->timezone;
} else {
    $timeZone = 'UTC';
}

$js = <<<JS
function renderUserTimeline(){
    const dt = new Date()
    const userListStr = [$userListStr];
    let startDateTime = '$startDateTime';
    let endDatetime = '$endDateTime';
    let timeZone = '$timeZone';
    
    
    $("#myTimeline").Timeline({
       type: "bar",
       startDatetime: startDateTime,
       endDatetime: endDatetime,
       scale: "hour",
       rows: "auto",
       //range: 2,
       //shift: true,
       zoom: true,
       minGridSize: 200,
       sidebar: {
           sticky:true,
            list: userListStr
            },
       ruler: {
            truncateLowers: false,
            top: {
                lines:      [ "month", "weekday", "day", "hour"], //, "minute"],
                height:     26,
                fontSize:   12,
                color:      "#333",
                background: "transparent",
                locale:     "en-US",
                format:     {
                    timeZone: timeZone, weekday: "short", year: "numeric", month: "long", day: "numeric", hour: "2-digit", minute: "2-digit"
                }
            },
            bottom: {
                lines:      [ "hour", "day" ],
                height:     26,
                fontSize:   12,
                color:      "#534",
                background: "transparent",
                locale:     "en-US",
                format:     {
                    timeZone: timeZone, day: "numeric", hour: "2-digit"
                }
            }
       },    
       headline: {
            display: true,
            title:   "User Monitor stats",
            range:   true,
            locale:  "en-US",
            format:  {
                timeZone: timeZone,
                custom: "%Y-%m-%d %H:%M"
            }
       },
       effects: {
            presentTime: true,
            hoverEvent:  true,
            stripedGridRow: true,
            horizontalGridStyle: "dotted",
            verticalGridStyle: "solid",
       },
    });
    }    
    renderUserTimeline()
    
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>

