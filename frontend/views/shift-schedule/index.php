<?php

use common\components\grid\DateTimeColumn;
use common\models\Employee;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel;
use modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\SearchUserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use src\helpers\setting\SettingHelper;
use yii\bootstrap4\Tabs;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */

/* @var $monthList array */
/* @var $scheduleTypeList ShiftScheduleType[] */
/* @var $scheduleTypeLabelList ShiftScheduleTypeLabel[] */

/* @var $scheduleSumData array */
/* @var $scheduleLabelSumData array */

/* @var $subtypeList array */
/* @var $userTimeZone string */
/* @var $user Employee */
/* @var $searchModel SearchUserShiftSchedule */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $assignedShifts UserShiftAssign[] */

/**
 * @var ShiftScheduleRequestSearch $searchModelPendingRequests
 * @var ActiveDataProvider $dataProviderPendingRequests
 */

$this->title = 'My Shift Schedule' . ' (' . $user->username . ')';
$this->params['breadcrumbs'][] = $this->title;

$bundle = \frontend\assets\FullCalendarAsset::register($this);
$scheduleTotalData = [];
$subtypeTotalData = [];
?>
<div class="shift-schedule-index">

    <h1><i class="fa fa-calendar"></i> <?= Html::encode($this->title) ?></h1>

    <p>
        <?php
            /** @abac ShiftAbacObject::ACT_MY_SHIFT_SCHEDULE, ShiftAbacObject::ACTION_ACCESS, Access to actions shift-schedule/* */
        if (\Yii::$app->abac->can(null, ShiftAbacObject::ACT_MY_SHIFT_SCHEDULE, ShiftAbacObject::ACTION_ACCESS)) :
            ?>
            <?= Html::a(
                '<i class="fa fa-plus-circle"></i> Generate Example Data',
                ['generate-example'],
                ['class' => 'btn btn-warning']
            ) ?>
            <?= Html::a(
                '<i class="fa fa-play-circle"></i> Generate User Schedule (' .
                SettingHelper::getShiftScheduleDaysLimit() . ' days' . ')',
                ['generate-user-schedule'],
                ['class' => 'btn btn-success'],
            ) ?>

            <?= Html::a('<i class="fa fa-remove"></i> Remove All User Schedule Data', ['remove-user-data'], [
            'class' => 'btn btn-danger',
            'data' => [
            'confirm' => 'Are you sure you want to delete all User Timelines?',
            'method' => 'post',
            ],
            ]) ?>
        <?php endif; ?>
        <?= Html::a(
            '<i class="fa fa-plus-circle"></i> Schedule Request',
            ['schedule-request-ajax'],
            ['class' => 'btn btn-success', 'id' => 'btn-schedule-request']
        ) ?>
        <?= Html::a(
            '<i class="fa fa-th-list"></i> Schedule Request History',
            ['schedule-request-history-ajax'],
            ['class' => 'btn btn-warning', 'id' => 'btn-schedule-request-history']
        ) ?>
        <?= Html::a(
            '<i class="fa fa-info-circle"></i> Legend',
            ['legend-ajax'],
            ['class' => 'btn btn-info', 'id' => 'btn-legend']
        ) ?>

    </p>

    <div class="row">
        <div class="col-md-6">
            <div class="x_panel">
                <div class="x_title">
                    <h2><i class="fa fa-calendar"></i> My Calendar (TimeZone: <?= Html::encode($userTimeZone)?>)</h2>
        <!--            <ul class="nav navbar-right panel_toolbox" style="min-width: initial;">-->
        <!--                <li>-->
        <!--                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>-->
        <!--                </li>-->
        <!--            </ul>-->
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

            <?= $this->render('partial/_pending_requests', [
                'dataProviderPendingRequests' => $dataProviderPendingRequests ?? null,
                    'searchModelPendingRequests' => $searchModelPendingRequests ?? null,
            ])?>

        </div>
        <div class="col-md-6">

            <div class="x_panel">
                <div class="x_title">
                    <h2><i class="fa fa-th"></i> My Assigned Shifts (TimeZone: <?php echo Yii::$app->formatter->timeZone?>)</h2>
                    <!--            <ul class="nav navbar-right panel_toolbox" style="min-width: initial;">-->
                    <!--                <li>-->
                    <!--                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>-->
                    <!--                </li>-->
                    <!--            </ul>-->
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" style="display: block">

                <?php if ($assignedShifts) :?>
                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center bg-info">
                                <th title="Name">Shift Name</th>
                                <th title="Schedule Rules">Schedules</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr class="text-center">
                            <td></td>
                            <th>
                                <div class="col-md-1"></div>
                                <div class="col-md-5 text-left">
                                    Name
                                </div>
                                <div class="col-md-2" title="Start Time">
                                    Start Time
                                </div>
                                <div class="col-md-2" title="End Time">
                                    End Time
                                </div>
                                <div class="col-md-2" title="Duration">
                                    Duration
                                </div>
                            </th>
                        </tr>
                        <?php foreach ($assignedShifts as $assignShift) :?>
                            <?php if (!$assignShift->shift->sh_enabled) {
                                continue;
                            }?>
                            <tr>
                                <th title="<?= Html::encode($assignShift->shift->sh_title)?>">
                                    <?= $assignShift->shift->getColorLabel(); ?>&nbsp; &nbsp;
                                    <?= Html::encode($assignShift->shift->sh_name)?>
                                </th>
                                <td>
                                    <?php if ($rules = $assignShift->shift->shiftScheduleRules) :?>
                                        <div class="row text-center">

                                            <?php foreach ($rules as $rule) :?>
                                                <?php if (!$rule->ssr_enabled) {
                                                    continue;
                                                }?>

                                                <div class="col-md-1"><?= ($rule->scheduleType ? $rule->scheduleType->getColorLabel() : '') ?></div>
                                                <div class="col-md-5 text-left" title="Expression: <?= Html::encode($rule->getCronExpression())?>, Exclude: <?= Html::encode($rule->getCronExpressionExclude())?>">
                                                    <?= Html::encode($rule->getScheduleTypeTitle())?>
                                                </div>
                                                <div class="col-md-2" title="Start Time"><i class="fa fa-clock-o"></i>
                                                    <?= $rule->ssr_start_time_utc ?
                                                        Yii::$app->formatter->asTime(strtotime($rule->ssr_start_time_utc))
                                                        : '-'
                                                    ?>
                                                </div>
                                                <div class="col-md-2" title="End Time"><i class="fa fa-clock-o"></i>
                                                    <?= $rule->ssr_end_time_utc ?
                                                        Yii::$app->formatter->asTime(strtotime($rule->ssr_end_time_utc))
                                                        : '-'
                                                    ?>
                                                </div>
                                                <div class="col-md-2" title="Duration">
                                                    <?= (round($rule->ssr_duration_time / 60, 1))?>h
                                                </div>

                                            <?php endforeach; ?>

                                        </div>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                </div>
            </div>


            <div class="x_panel">
                <div class="x_title">
                    <h2><i class="fa fa-bar-chart"></i> My Monthly scheduling statistics</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" style="display: block">

                    <div class="row">
                        <div class="col-md-12">

                            <div class="text-right">
                            <i class="fa fa-info-circle"></i> TimeLine statistics consists of statuses (
                                <?= Html::encode(UserShiftSchedule::getStatusNameById(UserShiftSchedule::STATUS_APPROVED))?>,
                                <?= Html::encode(UserShiftSchedule::getStatusNameById(UserShiftSchedule::STATUS_DONE))?>
                            )
                            </div>

                        <?php
                        try {
                            echo Tabs::widget([
                                'items' => [
                                    [
                                        'label' => 'Group by Types',
                                        'content' => $this->render('partial/_tab_types', [
                                            'monthList' => $monthList,
                                            'scheduleTypeList' => $scheduleTypeList,
                                            'scheduleSumData' => $scheduleSumData,
                                        ]),
                                        'active' => true
                                    ],
                                    [
                                        'label' => 'Group by Labels',
                                        'content' => $this->render('partial/_tab_labels', [
                                            'monthList' => $monthList,
                                            'scheduleTypeLabelList' => $scheduleTypeLabelList,
                                            'scheduleLabelSumData' => $scheduleLabelSumData,
                                        ]),
                                    ]
                                ]
                            ]);
                        } catch (Exception $e) {
                            echo 'Error: ' . $e->getMessage();
                        }
                        ?>

                        </div>
                    </div>

                </div>
            </div>

        <?php Pjax::begin(['id' => 'pjax-user-timeline']); ?>
        <div class="x_panel">
            <div class="x_title">
                <h2><i class="fa fa-bars"></i> My TimeLine List (<?=Html::encode($searchModel->clientStartDate)?> -
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
                    'columns' => [
                        [
                            'attribute' => 'uss_id',
                            'options' => ['style' => 'width:80px'],
                            'filter' => false
                        ],
                        [
                            'label' => 'Type',
                            'value' => static function (
                                UserShiftSchedule $model
                            ) {
                                return $model->shiftScheduleType ? $model->shiftScheduleType->getColorLabel() : '-';
                            },
                            'format' => 'raw',

                        ],
                        [
                            'attribute' => 'uss_sst_id',
                            'value' => static function (
                                UserShiftSchedule $model
                            ) {
                                return ($model->shiftScheduleType ? $model->shiftScheduleType->getIconLabel() . ' ' : '') . Html::a(
                                    $model->getScheduleTypeTitle(),
                                    null,
                                    ['class' => 'btn-open-timeline', 'data-tl_id' => $model->uss_id]
                                );
                            },
                            'format' => 'raw',
                            'filter' => ShiftScheduleType::getList()
                        ],
                        [
                            'label' => 'Start Date Time',
                            'class' => DateTimeColumn::class,
                            'attribute' => 'uss_start_utc_dt',
                            'format' => 'byUserDateTime',
                            'filter' => false
                        ],
//                        [
//                            'label' => 'start DT',
//                            'value' => static function (
//                                UserShiftSchedule $model
//                            ) {
//                                return date('Y-m-d [H:i]', strtotime($model->uss_start_utc_dt));
//                            },
//                            'options' => ['style' => 'width:180px']
//                        ],
                        [
                            'attribute' => 'uss_duration',
                            'value' => static function (
                                UserShiftSchedule $model
                            ) {
                                return Html::tag('span', round($model->uss_duration / 60, 1)
                                    . 'h', ['title' => Yii::$app->formatter->asDuration($model->uss_duration * 60)]);
                            },
                            'format' => 'raw',
                            'options' => ['style' => 'width:100px'],
                            'filter' => false
                        ],
                        [
                            'label' => 'End Date Time',
                            'class' => DateTimeColumn::class,
                            'attribute' => 'uss_end_utc_dt',
                            'format' => 'byUserDateTime',
                            'filter' => false
                        ],
//            'uss_duration',

//                        [
//                            'attribute' => 'uss_shift_id',
//                            'value' => static function (
//                                UserShiftSchedule $model
//                            ) {
//                                return $model->getShiftTitle();
//                            },
//                            'filter' => false //Shift::getList()
//                        ],

//                        [
//                            'attribute' => 'uss_ssr_id',
//                            'value' => static function (
//                                UserShiftSchedule $model
//                            ) {
//                                return $model->getRuleTitle();
//                            },
//                            'filter' => ShiftScheduleRule::getList()
//                        ],
                        //'uss_status_id',
                        [
                            'attribute' => 'uss_status_id',
                            'value' => static function (
                                UserShiftSchedule $model
                            ) {
                                return $model->getStatusName();
                            },
                            'filter' => UserShiftSchedule::getStatusList()
                        ],
//                        [
//                            'attribute' => 'uss_type_id',
//                            'value' => static function (
//                                UserShiftSchedule $model
//                            ) {
//                                return $model->getTypeName();
//                            },
//                            'filter' => UserShiftSchedule::getTypeList()
//                        ],
//            'uss_type_id',
//                        'uss_customized:boolean',
//                        [
//                            'class' => DateTimeColumn::class,
//                            'attribute' => 'uss_created_dt',
//                            'format' => 'byUserDateTime'
//                        ],
//            [
//                'class' => DateTimeColumn::class,
//                'attribute' => 'uss_updated_dt',
//                'format' => 'byUserDateTime'
//            ],

//            [
//                'class' => UserSelect2Column::class,
//                'attribute' => 'uss_updated_user_id',
//                'relation' => 'updatedUser',
//                'format' => 'username',
//                'placeholder' => 'Select User'
//            ],

                       // ['class' => 'yii\grid\ActionColumn'],
                    ],
                ]); ?>

            </div>
        </div>
        <?php Pjax::end(); ?>
    </div>
</div>


<?php

$ajaxUrl = \yii\helpers\Url::to(['shift-schedule/my-data-ajax']);
$openModalEventUrl = \yii\helpers\Url::to(['shift-schedule/get-event']);
// 'https://fullcalendar.io/api/demo-feeds/events.json?overload-day',

$js = <<<JS
    var shiftScheduleDataUrl = '$ajaxUrl';
    var openModalEventUrl = '$openModalEventUrl';
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
            openModalEventId(eventObj.id);
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
    
    function openModalEventId(id)
    {
        let modal = $('#modal-md');
        let eventUrl = openModalEventUrl + '?id=' + id;
        //modal.find('.modal-title').html('Offer [' + gid + '] status history');
        $('#modal-md-label').html('Schedule Event: ' + id);
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
    
    $(document).on('click', '#btn-schedule-request', function(e) {
        e.preventDefault();
        let modal = $('#modal-md');
        let url = processingUrlWithQueryParam($(this).attr('href'));
        $('#modal-md-label').html('<i class="fa fa-plus-circle"></i> Schedule Request');
        getRequest(modal, url);
        selectedRange = null;
    });
    
    $(document).on('click', '#btn-schedule-request-history', function(e) {
        e.preventDefault();
        let modal = $('#modal-md');
        let url = $(this).attr('href');
        $('#modal-md-label').html('<i class="fa fa-th-list"></i> Schedule Request History');
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
        openModalEventId(id);
    });
    
    $(document).on('ScheduleRequest:response', function (e, params) {
        if (params.requestStatus) {
            calendar.refetchEvents();
            updateTimeLinePendingList();
            $('#modal-md').modal('hide');
        }
    });
    
    $(document).on('RequestDecision:response', function (e, params) {
        if (params.requestStatus) {
            calendar.refetchEvents();
            updateTimeLineList();
            updateTimeLinePendingList();
            $('#modal-md').modal('hide');
        }
    });
    
JS;

$this->registerJs($js);
