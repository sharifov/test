<?php

use common\models\Employee;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\helpers\Html;

/* @var $this yii\web\View */

/* @var $user Employee */
/* @var $startDateTime string */
/* @var $endDateTime string */
/* @var $scheduleEventList array|UserShiftSchedule[] */
/* @var $userActiveEvents array */
/* @var $userOnlineEvents array */

\frontend\assets\Timeline2Asset::register($this);
?>

<div id="myTimeline">
    <ul class="timeline-events">
        <?php if (!empty($scheduleEventList)) : ?>
            <?php
            foreach ($scheduleEventList as $item) :?>
                <?php
                $tlData = [];
                $tlData['id'] = $item->uss_id;
                $tlData['row'] = 1;

                $tlData['extend'] = [
                    'toggle' => 'popover',
                    'trigger' => 'hover',
                    'html' => true
                ];

                $tlData['start'] = \common\models\Employee::convertTimeFromUtcToUserTime(
                    $user->getTimezone(),
                    strtotime($item->uss_start_utc_dt)
                );
                if ($item->uss_end_utc_dt) {
                    $tlData['end'] = \common\models\Employee::convertTimeFromUtcToUserTime(
                        $user->getTimezone(),
                        strtotime($item->uss_end_utc_dt)
                    );
                } else {
                    $tlData['size'] = 'small';
                }

                $tlData['bgColor'] = '#4075ab';
                $tlData['color'] = 'white';
                //$tlData['height'] = 30;
                $tlData['content'] = $item->getScheduleTypeTitle();
                ?>

                <li data-timeline-node='<?= \yii\helpers\Json::encode($tlData, JSON_THROW_ON_ERROR) ?>'>
                    <small>
                        <?php echo Html::encode(Yii::$app->formatter->asDateTime(
                            strtotime($item->uss_start_utc_dt),
                            'php: H:i'
                        )) ?>
                        -

                        <?php echo Html::encode(Yii::$app->formatter->asDateTime(
                            strtotime($item->uss_end_utc_dt),
                            'php: H:i'
                        )) ?>

                        #<?php echo Html::encode($item->uss_id)?>
                    </small>
                </li>
            <?php endforeach; ?>
            <?php ?>

        <?php endif; ?>

        <?php if (!empty($userOnlineEvents)) : ?>
            <?php
            foreach ($userOnlineEvents as $key => $item) :?>
                <?php
                $tlData = [];
                $tlData['id'] = $key;
                $tlData['row'] = 2;

                $tlData['extend'] = [
                    'toggle' => 'popover',
                    'trigger' => 'hover',
                    'html' => true
                ];

                $tlData['start'] = \common\models\Employee::convertTimeFromUtcToUserTime(
                    $user->getTimezone(),
                    strtotime($item['start'])
                );
                if ($item['end']) {
                    $tlData['end'] = \common\models\Employee::convertTimeFromUtcToUserTime(
                        $user->getTimezone(),
                        strtotime($item['end'])
                    );
                } else {
                    $tlData['size'] = 'small';
                }

                $tlData['bgColor'] = '#c4e1ae';
                $tlData['color'] = '#c4e1ae';
                //$tlData['height'] = 30;
                $tlData['content'] = Yii::$app->formatter->asDuration($item['duration'] * 60);
                ?>

                <li data-timeline-node='<?= \yii\helpers\Json::encode($tlData, JSON_THROW_ON_ERROR) ?>'>
                    <small> Online:
                        <?php echo Html::encode(Yii::$app->formatter->asDateTime(
                            strtotime($item['start']),
                            'php: H:i'
                        )) ?>
                        -

                        <?php echo Html::encode(Yii::$app->formatter->asDateTime(
                            strtotime($item['end']),
                            'php: H:i'
                        )) ?>
                    </small>
                </li>
            <?php endforeach; ?>
            <?php ?>

        <?php endif; ?>

        <?php if (!empty($userActiveEvents)) : ?>
            <?php
            foreach ($userActiveEvents as $key => $item) :?>
                <?php
                $tlData = [];
                $tlData['id'] = $key;
                $tlData['row'] = 2;

                $tlData['extend'] = [
                    'toggle' => 'popover',
                    'trigger' => 'hover',
                    'html' => true
                ];

                $tlData['start'] = \common\models\Employee::convertTimeFromUtcToUserTime(
                    $user->getTimezone(),
                    strtotime($item['start'])
                );
                if ($item['end']) {
                    $tlData['end'] = \common\models\Employee::convertTimeFromUtcToUserTime(
                        $user->getTimezone(),
                        strtotime($item['end'])
                    );
                } else {
                    $tlData['size'] = 'small';
                }

                $tlData['bgColor'] = '#51ad62';
                $tlData['color'] = '#51ad62';
                //$tlData['height'] = 30;
                $tlData['content'] = Yii::$app->formatter->asDuration($item['duration'] * 60);
                ?>

                <li data-timeline-node='<?= \yii\helpers\Json::encode($tlData, JSON_THROW_ON_ERROR) ?>'>
                    <small> Activity:
                        <?php echo Html::encode(Yii::$app->formatter->asDateTime(
                            strtotime($item['start']),
                            'php: H:i'
                        )) ?>
                        -

                        <?php echo Html::encode(Yii::$app->formatter->asDateTime(
                            strtotime($item['end']),
                            'php: H:i'
                        )) ?>
                    </small>
                </li>
            <?php endforeach; ?>
            <?php ?>

        <?php endif; ?>

        <!--                        <li data-timeline-node="{ id:12, row:1, start:'2022-07-29 09:10',end:'2022-07-30 14:41',content:'<p>Body...</p>', bgColor:'#CFC' }">Test 3</li>-->
        <!--                        <li data-timeline-node="{ id:14, row:1, start:'2022-07-29 10:19',end:'2022-07-30 13:41',content:'<p>Body...</p>', bgColor:'red',height:15 }" style="margin-top: 15px">Test 32</li>-->
        <!--                        <li data-timeline-node="{ id:22, row:2, start:'2022-07-29 14:10',end:'2022-07-30 12:30',relation:{before:12,linesize:20} }">-->
        <!--                            <span class="event-label">Test 4</span>-->
        <!--                        </li>-->

    </ul>
</div>
<!-- Timeline Event Detail View Area (optional) -->
<div class="timeline-event-view" style="color: #f8e7ab"></div>

<?php

//$userList = [];
//
//if (!empty($data['users'])) {
//    foreach ($data['users'] as $userId => $username) {
//        $userList[] = '\'<div style="margin: 0 10px 0 10px"><i class="fa fa-user"></i> ' . Html::encode($username) . ' (' . $userId . ') </div>\'';
//    }
//}
//
//$userListStr = implode(', ', $userList);


if (Yii::$app->user->identity->timezone) {
    $timeZone = Yii::$app->user->identity->timezone;
} else {
    $timeZone = 'UTC';
}

//$startDateTime = date('Y-m-d H:i', strtotime('-10 hours'));
//$endDateTime = date('Y-m-d H:i', strtotime('+34 hours'));

$js = <<<JS

function renderUserTimeline(){
    const dt = new Date()
    // const userListStr = [userListStr];
    let startDateTime = '$startDateTime';
    let endDatetime = '$endDateTime';
    let timeZone = '$timeZone';
    
    
    $("#myTimeline").Timeline({
       type: "bar",
       startDatetime: startDateTime,
       endDatetime: endDatetime,
       scale: "hour",
       rows: 2, //"auto",
       // range: 2,
       // shift: true,
       zoom: true,
       minGridSize: 50,
       // sidebar: {
       //     sticky:true,
       //      list: userListStr
       //      },
       ruler: {
            truncateLowers: false,
            top: {
                lines:      ["day", "hour"], //"month",, "minute"],
                height:     26,
                fontSize:   11,
                color:      "#333",
                background: "transparent",
                locale:     "en-US",
                format:     {
                    timeZone: timeZone, weekday: "short", year: "numeric", month: "long", hour: "2-digit", minute: "2-digit"
                }
            },
            bottom: {
                lines:      [ "hour", "weekday" ],
                height:     26,
                fontSize:   10,
                color:      "#534",
                background: "transparent",
                locale:     "en-US",
                format:     {
                    timeZone: timeZone, weekday: "long", day: "numeric", hour: "2-digit"
                }
            }
       },    
       headline: {
            display: true,
            title:   "My Shift Schedule Timeline and Activity",
            range:   true,
            locale:  "en-US",
            format:  {
                timeZone: timeZone,
                custom: "%d-%b [%H:00]"
            }
       },
       effects: {
            presentTime: true,
            hoverEvent:  true,
            stripedGridRow: true,
            horizontalGridStyle: "dotted",
            verticalGridStyle: "solid"
       }
    });
    }    
    renderUserTimeline();
JS;
$this->registerJs($js, \yii\web\View::POS_READY);