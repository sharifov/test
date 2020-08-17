<?php

use sales\model\user\entity\monitor\UserMonitor;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $data yii\data\ActiveDataProvider */
/* @var $searchModel \sales\model\user\entity\monitor\search\UserMonitorSearch */
/* @var $startDateTime string */
/* @var $endDateTime string */

$bundle = \frontend\assets\Timeline2Asset::register($this);

$this->title = 'Stats';

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);

$this->title = 'User Monitors';
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
?>
<?= $this->render('_stats_search', ['model' => $searchModel]); ?>

    <div id="myTimeline">
        <ul class="timeline-events">
            <?php if (!empty($data['items'])): ?>
                <?php /** @var UserMonitor $item */
                foreach ($data['items'] as $item):

//                    if ($item->um_type_id === UserMonitor::TYPE_LOGIN || $item->um_type_id === UserMonitor::TYPE_LOGOUT) {
//                        continue;
//                    }
                    $tlData = [];
                    $tlData['id'] = $item->um_id;
                    $tlData['row'] = $data['user2row'][$item->um_user_id] ?: 0;

                    $tlData['extend'] = [
                        'toggle' => 'popover',
                        'trigger' => 'hover',
                        //'html' => false
                    ];

                    $tlData['start'] = \common\models\Employee::convertTimeFromUtcToUserTime($user, strtotime($item->um_start_dt));
                    if ($item->um_end_dt) {
                        $tlData['end'] = \common\models\Employee::convertTimeFromUtcToUserTime($user, strtotime($item->um_end_dt)); //$item->um_end_dt;
                    } else {
                        $tlData['size'] = 'small';
                    }

                    $tlData['bgColor'] = $item->getTypeBgColor();

                    if ($item->um_type_id === UserMonitor::TYPE_LOGIN) {
                        $tlData['height'] = 40;
                        if (!$item->um_end_dt) {
                            $tlData['end'] = \common\models\Employee::convertTimeFromUtcToUserTime($user, strtotime($item->um_start_dt) + 90);
                        }

                        $tlData['content'] = $item->getTypeName() . ' ' . Yii::$app->formatter->asDatetime(strtotime($tlData['start']));
                    }

                    if ($item->um_type_id === UserMonitor::TYPE_LOGOUT) {
                        $tlData['content'] = $item->getTypeName() . ' ' . Yii::$app->formatter->asDatetime(strtotime($tlData['start']));
                    }

                    if ($item->um_type_id === UserMonitor::TYPE_ACTIVE) {
                        $tlData['height'] = 32;
                        if (!$item->um_end_dt) {
                            $tlData['end'] = \common\models\Employee::convertTimeFromUtcToUserTime($user, time());
                        }

                        $totalDuration = strtotime($tlData['end']) - strtotime($tlData['start']);

                        if ($totalDuration < 0) {
                            $totalDuration = 1;
                        }

                        $tlData['content'] = $item->getTypeName() . ' ' . gmdate('H:i:s', $totalDuration);
                    }

                    if ($item->um_type_id === UserMonitor::TYPE_ONLINE) {
                        if (!$item->um_end_dt) {
                            $tlData['end'] = \common\models\Employee::convertTimeFromUtcToUserTime($user, time());
                        }
                    }


                    $title = $item->um_id; //getTypeName()

                    ?>

                    <li data-timeline-node='<?= json_encode($tlData, JSON_THROW_ON_ERROR) ?>'></li>
                <?php endforeach; ?>
                <?php

                /*
                 *

                <li data-timeline-node="{ id:12, row:1, start:'2020-07-30 09:10',end:'2020-07-30 14:41',content:'<p>Body...</p>', bgColor:'#CFC',height:20 }" style="margin-top: 5px">Test 3</li>
            <li data-timeline-node="{ id:22, row:2, start:'2020-07-30 14:10',end:'2020-07-30 19:30',relation:{before:11,linesize:30} }">
                <span class="event-label">Test 2</span>
                <span class="event-content"><p>Test3</p></span>
            </li>*/
                ?>
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

//$(function () {


const dt = new Date()
const userListStr = [$userListStr];
let startDateTime = '$startDateTime';
let endDatetime = '$endDateTime';
let timeZone = '$timeZone';

//let defaults = {"type":"point","startDatetime":"2020-07-30","endDatetime":"2020-10-31","scale":"day","rows":"auto","minGridSize":48,
//    "headline":{"display":true,"title":"Demo of jQuery.Timeline","range":true,"locale":"en-US","format":{"timeZone":"Asia\/Tokyo"}},
//    "footer":{"display":true,"content":"<small>&copy; MAGIC METHODS 2020<\/small>","range":true,"locale":"en-US","format":{"timeZone":"Asia\/Tokyo"}},
//    "sidebar":{"sticky":true,
//    "list":["<a name=\"row-01\"><span class=\"avatar-icon\"><img src=\"imgs\/a4eg-thumb-001.png\" class=\"rounded\" alt=\"Tony Stark\"><\/span> \"Tony\" Stark<\/a>",
//    "<a name=\"row-02\"><span class=\"avatar-icon\"><img src=\"imgs\/a4eg-thumb-002.png\" class=\"rounded\" alt=\"Steve Rogers\"><\/span> Steve Rogers<\/a>"]},
//    
//    "ruler":{"top":
//    {"lines":["year","month","day","weekday"],"height":26,"fontSize":13,"color":"#777777","background":"#FFFFFF","locale":"en-US","format":{"timeZone":"Asia\/Tokyo","hour12":false,"year":"numeric","month":"long","day":"numeric","weekday":"short"}},
//    "bottom":
//    {"lines":["week","year"],"color":"#777777","background":"#FFFFFF","locale":"en-US","format":{"timeZone":"Asia\/Tokyo","hour12":false,"year":"numeric","week":"ordinal"}}},
//    "rangeAlign":"end","eventMeta":{"display":false,"scale":"day","locale":"en-US","format":{"timeZone":"Asia\/Tokyo"},"content":""},"reloadCacheKeep":false,"zoom":false,"debug":false},
//    
//    overrides = {
//        startDatetime: '2021-07-30',
//        endDatetime: '2021-12-31',
//        scale: 'year',
//        minGridSize: 240,
//        headline: {
//            title: 'Test',
//            range: true,
//        },
//        footer: {
//            title: 'Test footer',
//            display: true,
//        },  
//        ruler: {
//            top: {
//                lines: [ 'year' ],
//                format: { month: 'numeric' }
//            },
//            bottom: {
//                lines: [ 'year' ],
//                format: { month: 'short' }
//            }
//        },
//        effects: {
//            hoverEvent: true,
//        },
//        reloadCacheKeep: true,
//        zoom: true,
//        debug: false
//    },
//    mcu_options = Object.assign( defaults, overrides )
    
//$('#myTimeline').Timeline( );

// $('#myTimeline').Timeline( mcu_options )
// .Timeline('initialized', function(e,v){
//     //$('.jqtl-headline-wrapper').append('<div><a href="/" class="btn btn-secondary btn-sm">&laquo; Home</a></div>')
//     //$('[data-toggle="popover"]').popover()
// })



//})

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
                custom: "%Y-%b-%m %H:%I"
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
JS;
$this->registerJs($js, \yii\web\View::POS_READY);

?>