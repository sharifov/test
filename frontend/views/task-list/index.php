<?php

use common\components\grid\DateTimeColumn;
use common\models\Employee;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\SearchUserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskHelper;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $userTimeZone string */
/* @var $user Employee */
/* @var $searchModel SearchUserShiftSchedule */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $startDateTime string */
/* @var $endDateTime string */
/* @var $scheduleEventList array|UserShiftSchedule[] */


$this->title = 'My Task List' . ' (' . $user->username . ')';
$this->params['breadcrumbs'][] = $this->title;

\frontend\assets\FullCalendarAsset::register($this);
\frontend\assets\TimerAsset::register($this);
\frontend\assets\Timeline2Asset::register($this);

$scheduleTotalData = [];
$subtypeTotalData = [];
?>
<style>
    .datepicker-dropdown {
        z-index:21!important
    }
</style>
<div class="task-list-index">

    <h1><i class="fa fa-check-square-o"></i> <?= Html::encode($this->title) ?></h1>

    <div class="col-md-6">
    </div>
    <div class="col-md-6 text-right">
        <?= Html::a(
            '<i class="fa fa-info-circle"></i> Legend',
            ['/shift-schedule/legend-ajax'],
            ['class' => 'btn btn-info', 'id' => 'btn-legend']
        ) ?>
    </div>

    <div class="row">
        <div class="col-md-12">
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
                    <!--                        <li data-timeline-node="{ id:12, row:1, start:'2022-07-29 09:10',end:'2022-07-30 14:41',content:'<p>Body...</p>', bgColor:'#CFC' }">Test 3</li>-->
                    <!--                        <li data-timeline-node="{ id:14, row:1, start:'2022-07-29 10:19',end:'2022-07-30 13:41',content:'<p>Body...</p>', bgColor:'red',height:15 }" style="margin-top: 15px">Test 32</li>-->
                    <!--                        <li data-timeline-node="{ id:22, row:2, start:'2022-07-29 14:10',end:'2022-07-30 12:30',relation:{before:12,linesize:20} }">-->
                    <!--                            <span class="event-label">Test 4</span>-->
                    <!--                        </li>-->

                </ul>
            </div>

            <!-- Timeline Event Detail View Area (optional) -->
            <div class="timeline-event-view" style="color: #f8e7ab"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">



            <?php Pjax::begin(['id' => 'pjax-user-timeline']); ?>
            <div class="x_panel">
                <div class="x_title">
                    <h2><i class="fa fa-bars"></i> My Task List (<?=Html::encode($searchModel->clientStartDate)?> -
                        <?=Html::encode($searchModel->clientEndDate)?>)</h2>
                    <!--            <ul class="nav navbar-right panel_toolbox" style="min-width: initial;">-->
                    <!--                <li>-->
                    <!--                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>-->
                    <!--                </li>-->
                    <!--            </ul>-->
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" style="display: block">


                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
                        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
                        'rowOptions' => static function (UserTask $model) {
                            if ($model->isDelay()) {
                                return [
                                    'class' => 'bg-info'
                                ];
                            }
                            if ($model->isDeadline()) {
                                return [
                                    'class' => 'danger'
                                ];
                            }
                            return [];
                        },
                        'columns' => [
                            [
                                'attribute' => 'ut_id',
                                'value' => static function (UserTask $model) {
                                    return $model->ut_id;
                                },
                                'options' => ['style' => 'width:80px']
                            ],
                            [
                                'attribute' => 'ut_priority',
                                'value' => static function (UserTask $model) {
                                    return UserTaskHelper::priorityLabel($model->ut_priority);
                                },
                                'format' => 'raw',
                                'filter' => UserTask::PRIORITY_LIST,
                            ],

                            [
                                'attribute' => 'ut_status_id',
                                'value' => static function (UserTask $model) {
                                    return UserTaskHelper::statusLabel($model->ut_status_id);
                                },
                                'format' => 'raw',
                                'filter' => UserTask::STATUS_LIST,
                            ],
                            [
                                'label' => 'Shift Events',
                                'value' => static function (UserTask $model) {
                                    if ($model->shiftScheduleEventTasks) {
                                        $data = [];
                                        foreach ($model->userShiftEvents as $event) {
                                            //$data[] = $event->uss_id . ' ('. $event->getShiftName().')';

                                            $data[] =  Html::a(
                                                '[' . $event->uss_id . '] ' . $event->getScheduleTypeTitle(),
                                                null,
                                                ['class' => 'btn-open-timeline', 'data-tl_id' => $event->uss_id]
                                            );
                                        }
                                        return implode(', ', $data); //\yii\helpers\VarDumper::dumpAsString($model->userShiftEvents);
                                    }
                                    return '-';
                                },
                                //'filter' => TargetObject::TARGET_OBJ_LIST,
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'taskName',
                                'label' => 'Task Name',
                                'value' => static function (UserTask $model) {
                                    if (!$model->ut_task_list_id) {
                                        return '-'; //Yii::$app->formatter->nullDisplay;
                                    }
                                    return Html::tag(
                                        'span',
                                        $model->taskList->tl_title ?: '-',
                                        ['title' => 'Task List ID: ' . $model->ut_task_list_id]
                                    );
                                },
                                'format' => 'raw',
                            ],

                            [
                                'attribute' => 'ut_target_object',
                                'label' => 'Object',
                                'value' => static function (UserTask $model) {
                                    if (!$model->ut_target_object) {
                                        return Yii::$app->formatter->nullDisplay;
                                    }
                                    return $model->ut_target_object;
                                },
                                'filter' => TargetObject::TARGET_OBJ_LIST,
                                'format' => 'raw',
                            ],

                            [
                                'attribute' => 'ut_target_object_id',
                                'label' => 'Target',
                                'value' => static function (UserTask $model) {
                                    return TargetObject::getTargetLink(
                                        $model->ut_target_object,
                                        $model->ut_target_object_id
                                    );
                                },
                                // 'filter' => TargetObject::TARGET_OBJ_LIST,
                                'format' => 'raw',
                            ],
                            //'ut_target_object_id',

                            [
                                'label' => 'Duration',
                                'value' => static function (UserTask $model) {
                                    return UserTaskHelper::getDuration($model->ut_start_dt, $model->ut_end_dt);
                                },
                                'format' => 'raw',
                            ],
                            [
                                'label' => 'Delay',
                                'value' => static function (UserTask $model) {
                                    return $model->isDelay() ?
                                        UserTaskHelper::getDelayTimer($model->ut_start_dt, $model->ut_end_dt) :
                                        '-';
                                },
                                'format' => 'raw',
                            ],

                            [
                                'class' => DateTimeColumn::class,
                                'limitEndDay' => false,
                                'attribute' => 'ut_start_dt',
                                'format' => 'byUserDateTimeAndUTC',
                            ],
                            [
                                'label' => 'Deadline',
                                'value' => static function (UserTask $model) {
                                    return $model->isDeadline() ? Html::tag(
                                        'span',
                                        'Deadline',
                                        ['title' => \Yii::$app->formatter->asRelativeTime(strtotime($model->ut_end_dt)),
                                            'class' => 'badge badge-danger']
                                    ) :
                                        UserTaskHelper::getDeadlineTimer($model->ut_start_dt, $model->ut_end_dt);
                                },
                                'format' => 'raw',
                            ],
                            [
                                'class' => DateTimeColumn::class,
                                'limitEndDay' => false,
                                'attribute' => 'ut_end_dt',
                                'format' => 'byUserDateTimeAndUTC',
                            ],


//                            [
//                                'class' => DateTimeColumn::class,
//                                'limitEndDay' => false,
//                                'attribute' => 'ut_created_dt',
//                                'format' => 'byUserDateTimeAndUTC',
//                            ],
                            //'ut_year',
                            //'ut_month',
//                            [
//                                'class' => ActionColumn::class,
//                                'urlCreator' => static function ($action, UserTask $model, $key, $index, $column) {
//                                    return Url::toRoute([$action, 'ut_id' => $model->ut_id,
//                                        'ut_year' => $model->ut_year,
//                                        'ut_month' => $model->ut_month]);
//                                }
//                            ],
                        ],
                    ]); ?>



                </div>
            </div>
            <?php Pjax::end(); ?>
        </div>
        <div class="col-md-5">
            <div class="x_panel">
                <div class="x_title">
                    <h2><i class="fa fa-calendar"></i> My Calendar (TimeZone: <?= Html::encode($userTimeZone)?>)</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" style="display: block">
                    <div class="row">
                        <div class="col-md-12">
                            <div id='calendar'></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<?php

$ajaxUrl = \yii\helpers\Url::to(['task-list/my-data-ajax']);
$openModalEventUrl = \yii\helpers\Url::to(['shift-schedule/ajax-event-details']);
$openModalUserTaskUrl = \yii\helpers\Url::to(['task-list/ajax-user-task-details']);
// 'https://fullcalendar.io/api/demo-feeds/events.json?overload-day',

$js = <<<JS
    var shiftScheduleDataUrl = '$ajaxUrl';
    var openModalEventUrl = '$openModalEventUrl';
    var openModalUserTaskUrl = '$openModalUserTaskUrl';
    var calendarEl = document.getElementById('calendar');
    var selectedRange = null;
    var calendar = new FullCalendar.Calendar(calendarEl, {
        //initialView: 'dayGridWeek',
        initialView: 'dayGridMonth',
        height: 800,
        navLinks: true,
        displayEventEnd: true,
        //nextDayThreshold: '09:00:00',
        
        // initialDate: '2022-04-05',
        // eventColor: 'green',
          eventTimeFormat: { // like '14:30:00'
            hour: '2-digit',
            minute: '2-digit',
            //second: '2-digit',
            //meridiem: false
            hour12: false
          },

      eventDidMount: function(info) {
           // $(info.el).tooltip();
           
           if (info.event.extendedProps.icon.length > 0) {
                $(info.el).find('.fc-event-title').prepend('<i class="' + info.event.extendedProps.icon + '"></i> ');
           }
           
           //info.el.title = '<i class="fa fa-clock"></i> ' + info.el.title;
           
           if (info.event.extendedProps.description.length > 0) {
                $(info.el).tooltip({ "title": info.event.extendedProps.description});
           }
           
           
           //info.event.title = '123';
            //console.log(info.event.icon);
              // $(info.el).find('.fc-title').prepend('<i class="' + info.event.icon + '"></span> ');
      },

        
        // navLinkDayClick: function(date, jsEvent) {
        //     console.log('day', date.toISOString());
        //     console.log('coords', jsEvent.pageX, jsEvent.pageY);
        //   },
        
        navLinkWeekClick: function(weekStart, jsEvent) {
            console.log('week start', weekStart.toISOString());
            console.log('coords', jsEvent.pageX, jsEvent.pageY);
          },
        // themeSystem: 'bootstrap',
        // customTimeGridDay
        firstDay: 1,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title', // buttons for switching between views
            right: 'dayGridMonth,customDayGridWeek,timeGridWeek,timeGridDay', //,listDay,listWeek,listMonth
        },
     
      
      //   footerToolbar: {
      //       left: 'custom1',
      //       center: '',
      //       right: 'listDay,listWeek,listMonth'
      //   },
      // customButtons: {
      //   custom1: {
      //     text: 'custom 1',
      //     click: function() {
      //       alert('clicked custom button 1!');
      //     }
      //   }
      // },
      
      slotLabelFormat: {hour: 'numeric', minute: '2-digit', hour12: false, meridiem: 'short', omitZeroMinute: false},
      
      views: {
            dayGridMonth: { // name of view
              //titleFormat: { year: 'numeric', month: '2-digit', day: '2-digit' }
              // other view-specific options here
            },
            
            dayGrid: {
              // options apply to dayGridMonth, dayGridWeek, and dayGridDay views
            },
            timeGrid: {

              // options apply to timeGridWeek and timeGridDay views
            },
            week: {
              // options apply to dayGridWeek and timeGridWeek views
            },
            day: {
                 
              // options apply to dayGridDay and timeGridDay views
            },
            
            
        // customTimeGridDay: {
        //   type: 'timeGrid',
        //   duration: { days: 7 },
        //   buttonText: '7 days'
        // },
        customDayGridWeek: {
          type: 'dayGridWeek',
          buttonText: 'WeekDay'
        }
        
        // listDay: { buttonText: 'list day' },
        // listWeek: { buttonText: 'list week' },
        // listMonth: { buttonText: 'list month' }
      },
      
      //plugins: [ 'dayGridPlugin' ],
        timeZone: '$userTimeZone',
        locale: 'en-GB',
        dayMaxEvents: true, // allow "more" link when too many events
        //events: shiftScheduleDataUrl,
         eventSources: [

            // your event source
            {
              url: shiftScheduleDataUrl,
              method: 'GET',
              /*extraParams: {
                custom_param1: 'something',
                custom_param2: 'somethingelse'
              },*/
              failure: function() {
                alert('There was an error while fetching events!');
              }
              // color: 'yellow',   // a non-ajax option
              // textColor: 'black' // a non-ajax option
            }
        
            // any other sources...
        
          ],
        
          //events: 'https://fullcalendar.io/api/demo-feeds/events.json',
          editable: false,
          selectable: true,
        
        //events: 'https://fullcalendar.io/api/demo-feeds/events.json?with-resources=2',
        // events: [
        //     { start: '2022-04-06T12:30:00Z' }, // will be shifted to local
        //     { start: '2022-04-07T12:30:00' }, // already same offset as local, so won't shift
        //     { start: '2022-04-08T12:30:00' } // will be parsed as if it were '2018-09-01T12:30:00+XX:XX'
        //   ],
        //   dateClick: function(arg) {
        //    
        //     // console.log('Clicked on: ' + arg.dateStr);
        //     // console.log('Coordinates: ' + arg.jsEvent.pageX + ',' + arg.jsEvent.pageY);
        //     // console.log('Current view: ' + arg.view.type);
        //     //console.log('Resource ID: ' + arg.resource.id);
        //     console.log(arg);
        //     // change the day's background color just for fun
        //     // arg.dayEl.style.backgroundColor = 'red';
        //    
        //     //console.log(arg.date.toString()); // use *local* methods on the native Date Object
        //     // will output something like 'Sat Sep 01 2018 00:00:00 GMT-XX:XX (Eastern Daylight Time)'
        //   },
          eventClick: function(info) {
            info.jsEvent.preventDefault();
            var eventObj = info.event;
            
            var typeEvent = 'user-shift-schedule';
            
            if(eventObj.extendedProps.typeEvent !== 'undefined'){
                typeEvent = eventObj.extendedProps.typeEvent
            }
            openModalEventId(eventObj.id, typeEvent);
          },
          select: function(info) {
            updateTimeLineList(info.startStr, info.endStr);
            // console.log(info);
            // console.log('selected ' + info.startStr + ' to ' + info.endStr);
            selectedRange = {
                start: info.startStr,
                end: info.endStr
            }
          }
    });

    // var calendar = new FullCalendar.Calendar(calendarEl, {
    //     initialView: 'dayGridMonth'
    // });
    calendar.render();
    
    function openModalEventId(id, typeEvent)
    {
        let modal = $('#modal-md');
        let eventUrl = openModalEventUrl + '?id=' + id;
        let title = 'Schedule Event: ';
       
        if(typeEvent == "user-task"){
             eventUrl = openModalUserTaskUrl + '?id=' + id;
             title = 'User Task: '
        }
      
        //modal.find('.modal-title').html('Offer [' + gid + '] status history');
        $('#modal-md-label').html(title + id);
        modal.find('.modal-body').html('');
        modal.find('.modal-body').load(eventUrl, function( response, status, xhr ) {
            if (status === 'error') {
                alert(response);
            } else {
                modal.modal('show');
            }
        });
    }
    
    
    $(document).on('click', '#btn-legend', function(e) {
        e.preventDefault();
        let modal = $('#modal-md');
        let url = $(this).attr('href');
        $('#modal-md-label').html('<i class="fa fa-info-circle"></i> Schedule Legend');
        getRequest(modal, url);
    });
    
    
    function getRequest(modal, url) {
        modal.find('.modal-body').html(loaderTemplate);
        modal.modal('show');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            if (status === 'error') {
                modal.modal('hide');
                alert(response);
            }   
        });
    }
    
    function loaderTemplate(modal) {
        return '<div class="text-center"> \
                    <div class="spinner-border m-5" role="status"> \
                        <span class="sr-only">Loading...</span> \
                    </div> \
                </div>';
    }
    
    function processingUrlWithQueryParam(url) {
        if (!selectedRange) {
            return url;
        }

        var end = new Date(selectedRange.end);
        end.setDate(end.getDate() - 1);
        end.setHours(23, 59);
        var data = {
            start: selectedRange.start,            
            end: end.getFullYear() + '-' + ('0' + (end.getMonth() + 1)).slice(-2) + '-' + ('0' + end.getDate()).slice(-2) + ' ' + end.getHours() + ':' + end.getMinutes()           
        };
        
        var prefix = '?';
        if (url.indexOf('?') !== -1) {
            prefix = '&';
        }
        return url + prefix + $.param(data);
    }

    function updateTimeLineList(startDate, endDate) 
    {
        $.pjax.reload({container: '#pjax-user-timeline', push: false, replace: false, timeout: 5000, data: {startDate: startDate, endDate: endDate}});
    }
    
    function updateTimeLinePendingList() 
    {
        $.pjax.reload({container: '#pjax-schedule-pending-request', push: false, replace: false, timeout: 5000});
    }
    
    $('body').off('click', '.btn-open-timeline').on('click', '.btn-open-timeline', function (e) {
        e.preventDefault();
        let id = $(this).data('tl_id');
        openModalEventId(id, 'user-shift-schedule');
    });
          
     $(document).on('pjax:end', function() {
         $('[data-toggle="tooltip"]').tooltip();
    });

    function startTimers() {
    
        $(".timer").each(function( index ) {
            var sec = $( this ).data('sec');
            var control = $( this ).data('control');
            var format = $( this ).data('format');
            $(this).timer({countdown: true, format: format, duration: sec}).timer(control);
        });
    
        //$('.timer').timer('remove');
        //$('.timer').timer({format: '%M:%S', seconds: 0}).timer('start');
    }

    $(document).on('pjax:start', function() {
        //$("#modalUpdate .close").click();
    });

    $(document).on('pjax:end', function() {
        startTimers();
    });
    
    startTimers();
JS;

$this->registerJs($js);


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
       rows: 1, //"auto",
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
            title:   "My Shift Schedule Timeline",
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