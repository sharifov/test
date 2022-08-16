<?php

use common\models\Employee;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\helpers\Html;

/* @var $this yii\web\View */

/* @var $user Employee */

/* @var $startDateTime string */
/* @var $endDateTime string */
/* @var $startDateTimeCalendar string */
/* @var $endDateTimeCalendar string */

/* @var $scheduleEventList array|UserShiftSchedule[] */
/* @var $userActiveEvents array */
/* @var $userOnlineEvents array */
/* @var $userOnlineData array */

\frontend\assets\Timeline2Asset::register($this);
?>


<?php
   // \yii\helpers\VarDumper::dump($userOnlineData, 10, true);
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
        <?php endif; ?>



        <?php if (!empty($userOnlineData)) : ?>
            <?php
            foreach ($userOnlineData as $shiftId => $typeData) :?>
                <?php
                foreach ($typeData as $type => $data) :?>
                    <?php
                    foreach ($data as $item) :?>
                        <?php
                        if (!$item) {
                            continue;
                        }
                    // \yii\helpers\VarDumper::dump($item, 10, true); exit;
                        $tlData = [];
                        $tlData['id'] = $shiftId . '-' . md5($item['start']);
                        $tlData['row'] = 3;

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
                        switch ($type) {
                            case 'earlyStart':
                                $tlData['bgColor'] = '#ffc33b';
                                break;
                            case 'earlyFinish':
                                $tlData['bgColor'] = 'red';
                                break;
                            case 'lateStart':
                                $tlData['bgColor'] = 'red';
                                break;
                            case 'lateFinish':
                                $tlData['bgColor'] = '#ffc33b';
                                break;
                            case 'usefulTime':
                                $tlData['bgColor'] = '#c4e1ae';
                                break;
                        }



                        $tlData['color'] = 'white';
                    //$tlData['height'] = 30;
                        $tlData['content'] = Yii::$app->formatter->asDuration($item['duration'] * 60);
                        ?>

                <li data-timeline-node='<?= \yii\helpers\Json::encode($tlData, JSON_THROW_ON_ERROR) ?>'>
                    <small> <?= Html::encode($type) ?>:
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
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>



        <!--                        <li data-timeline-node="{ id:12, row:1, start:'2022-07-29 09:10',end:'2022-07-30 14:41',content:'<p>Body...</p>', bgColor:'#CFC' }">Test 3</li>-->
        <!--                        <li data-timeline-node="{ id:14, row:1, start:'2022-07-29 10:19',end:'2022-07-30 13:41',content:'<p>Body...</p>', bgColor:'red',height:15 }" style="margin-top: 15px">Test 32</li>-->
        <!--                        <li data-timeline-node="{ id:22, row:2, start:'2022-07-29 14:10',end:'2022-07-30 12:30',relation:{before:12,linesize:20} }">-->
        <!--                            <span class="event-label">Test 4</span>-->
        <!--                        </li>-->

    </ul>
</div>
<!-- Timeline Event Detail View Area (optional) -->
<div class="timeline-event-view"></div>

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


if ($user->getTimezone()) {
    $timeZone = $user->getTimezone();
} else {
    $timeZone = 'UTC';
}
 //'UTC';
//echo $timeZone;
//$timeZone = 'Europe/Chisinau';
//$timeZone = 'America/Denver';
//$timeZone = 'Etc/GMT-6';


$labelList[] = '\'<div style="margin: 0 10px 0 10px"><i class="fa fa-calendar"></i> Shift Schedule  </div>\'';
$labelList[] = '\'<div style="margin: 0 10px 0 10px"><i class="fa fa-clock-o"></i> Online, Activity </div>\'';
$labelList[] = '\'<div style="margin: 0 10px 0 10px"><i class="fa fa-clock-o"></i> Early, Late </div>\'';
$labelListStr = implode(', ', $labelList);


$startDateTimeFormat = date('d-M [H:i]', strtotime($startDateTimeCalendar));
$endDateTimeFormat = date('d-M [H:i]', strtotime($endDateTimeCalendar));

$js = <<<JS

function renderUserTimeline(){
    const dt = new Date()
    // const userListStr = [userListStr];
    let startDateTime = '$startDateTimeCalendar';
    let endDatetime = '$endDateTimeCalendar';
    let timeZone = '$timeZone';
    const labelListStr = [$labelListStr];
    
    
    $("#myTimeline").Timeline({
       type: "bar",
       startDatetime: startDateTime,
       endDatetime: endDatetime,
       scale: "hour",
       rows: 3, //"auto",
       // range: 2,
       // shift: true,
       zoom: true,
       minGridSize: 100,
       sidebar: {
            sticky:true,
            list: labelListStr
        },
       ruler: {
            truncateLowers: false,
            top: {
                lines:      ["day", "hour"], //"month",, "minute"],
                height:     26,
                fontSize:   12,
                color:      "#333",
                //background: "transparent",
                locale:     "en-US",
                format:     {
                    timeZone: timeZone, weekday: "short", year: "numeric", month: "long", hour: "2-digit", minute: "2-digit"
                }
            },
            bottom: {
                lines:      [ "hour", "weekday" ],
                height:     26,
                fontSize:   11,
                color:      "#534",
                //background: "transparent",
                locale:     "en-US",
                format:     {
                    timeZone: timeZone, weekday: "long", day: "numeric", hour: "2-digit"
                }
            }
       },    
       headline: {
            display: true,
            title:   "My Shift Schedule Timeline and Activity ($startDateTimeFormat - $endDateTimeFormat)",
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