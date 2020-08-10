<?php
use yii\helpers\Html;

$bundle = \frontend\assets\Timeline2Asset::register($this);

$this->title = 'Timeline';

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);
?>


<?php
    $currentDateTS = strtotime(Yii::$app->formatter->asDate(time()));
    $startTime = date('Y-m-d H:i');
//    echo $startTime;
    $endTime = date('Y-m-d H:i', strtotime($startTime) + (60 * 60));
?>


    <div id="myTimeline">
        <ul class="timeline-events">
            <li data-timeline-node="{ id:11, row:1, start:'2020-07-30 08:45',end:'2020-07-30 16:30',content:'<p>Event Body...</p>', bgColor:'#f8e7ab' }">Test 1</li>
            <li data-timeline-node="{ id:12, row:1, start:'2020-07-30 09:10',end:'2020-07-30 14:41',content:'<p>Body...</p>', bgColor:'#CFC',height:20 }" style="margin-top: 5px">Test 3</li>
            <li data-timeline-node="{ id:22, row:2, start:'2020-07-30 14:10',end:'2020-07-30 19:30',relation:{before:11,linesize:30} }">
                <span class="event-label">Test 2</span>
                <span class="event-content"><p>Test3</p></span>
            </li>
        </ul>
    </div>


    <!-- Timeline Event Detail View Area (optional) -->
    <div class="timeline-event-view" style="color: #f8e7ab"></div>



<?php

$js = <<<JS

//$(function () {


const dt = new Date()

let defaults = {"type":"point","startDatetime":"2020-07-30","endDatetime":"2020-10-31","scale":"day","rows":"auto","minGridSize":48,
    "headline":{"display":true,"title":"Demo of jQuery.Timeline","range":true,"locale":"en-US","format":{"timeZone":"Asia\/Tokyo"}},
    "footer":{"display":true,"content":"<small>&copy; MAGIC METHODS 2020<\/small>","range":true,"locale":"en-US","format":{"timeZone":"Asia\/Tokyo"}},
    "sidebar":{"sticky":true,
    "list":["<a name=\"row-01\"><span class=\"avatar-icon\"><img src=\"imgs\/a4eg-thumb-001.png\" class=\"rounded\" alt=\"Tony Stark\"><\/span> \"Tony\" Stark<\/a>",
    "<a name=\"row-02\"><span class=\"avatar-icon\"><img src=\"imgs\/a4eg-thumb-002.png\" class=\"rounded\" alt=\"Steve Rogers\"><\/span> Steve Rogers<\/a>"]},
    
    "ruler":{"top":
    {"lines":["year","month","day","weekday"],"height":26,"fontSize":13,"color":"#777777","background":"#FFFFFF","locale":"en-US","format":{"timeZone":"Asia\/Tokyo","hour12":false,"year":"numeric","month":"long","day":"numeric","weekday":"short"}},
    "bottom":
    {"lines":["week","year"],"color":"#777777","background":"#FFFFFF","locale":"en-US","format":{"timeZone":"Asia\/Tokyo","hour12":false,"year":"numeric","week":"ordinal"}}},
    "rangeAlign":"end","eventMeta":{"display":false,"scale":"day","locale":"en-US","format":{"timeZone":"Asia\/Tokyo"},"content":""},"reloadCacheKeep":false,"zoom":false,"debug":false},
    
    overrides = {
        startDatetime: '2021-07-30',
        endDatetime: '2021-12-31',
        scale: 'year',
        minGridSize: 240,
        headline: {
            title: 'Test',
            range: true,
        },
        footer: {
            title: 'Test footer',
            display: true,
        },  
        ruler: {
            top: {
                lines: [ 'year' ],
                format: { month: 'numeric' }
            },
            bottom: {
                lines: [ 'year' ],
                format: { month: 'short' }
            }
        },
        effects: {
            hoverEvent: true,
        },
        reloadCacheKeep: true,
        zoom: true,
        debug: false
    },
    mcu_options = Object.assign( defaults, overrides )
    
//$('#myTimeline').Timeline( );

// $('#myTimeline').Timeline( mcu_options )
// .Timeline('initialized', function(e,v){
//     //$('.jqtl-headline-wrapper').append('<div><a href="/" class="btn btn-secondary btn-sm">&laquo; Home</a></div>')
//     //$('[data-toggle="popover"]').popover()
// })



//})

    $("#myTimeline").Timeline({
       type:"bar",
       startDatetime: "2020-07-30 02:00",
       //endDatetime: "2020-07-31",
       endDatetime: "auto",
       scale: "hour",
       rows: 2,
       //range: 2,
       //shift: true,
       // zoom: true,
       //"minGridSize":164,
       sidebar: {
           sticky:true,
            list:[
                '<div style="margin: 0 10px 0 10px"><i class="fa fa-user"></i> User 1</div>',
                '<div style="margin: 0 10px 0 10px"><i class="fa fa-user"></i> User 2</div>',
                ]},
       ruler: {
            truncateLowers: false,
            top: {
                lines:      [ "month", "weekday", "day", "hour"], //, "minute"],
                height:     26,
                fontSize:   12,
                color:      "#333",
                background: "transparent",
                locale:     "en-GB",
                format:     {
                    timeZone: "UTC", weekday: "short", year: "numeric", month: "long", day: "numeric", hour: "2-digit", minute: "2-digit"
                }
            },
            bottom: {
                lines:      [ "hour", "day" ],
                height:     26,
                fontSize:   12,
                color:      "#333",
                background: "transparent",
                locale:     "en-GB",
                format:     {
                    timeZone: "UTC", day: "numeric", hour: "2-digit"
                }
            }
       },    
       headline: {
            display: true,
            title:   "Timeline User Activity",
            range:   true,
            locale:  "en-US",
            format:  {
                timeZone: "UTC",
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