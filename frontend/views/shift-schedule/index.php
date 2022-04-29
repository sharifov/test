<?php

use common\components\grid\DateTimeColumn;
use common\models\Employee;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\SearchUserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use src\helpers\setting\SettingHelper;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */

/* @var $monthList array */
/* @var $scheduleTypeList ShiftScheduleType[] */
/* @var $scheduleSumData array */
/* @var $subtypeList array */
/* @var $userTimeZone string */
/* @var $user Employee */
/* @var $searchModel SearchUserShiftSchedule */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $assignedShifts UserShiftAssign[] */

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
                    <!--            <ul class="nav navbar-right panel_toolbox" style="min-width: initial;">-->
                    <!--                <li>-->
                    <!--                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>-->
                    <!--                </li>-->
                    <!--            </ul>-->
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" style="display: block">

                    <div class="col-md-12">
                        <i class="fa fa-info-circle"></i> TimeLine statistics consists of statuses (
                        <?= Html::encode(UserShiftSchedule::getStatusNameById(UserShiftSchedule::STATUS_APPROVED))?>,
                        <?= Html::encode(UserShiftSchedule::getStatusNameById(UserShiftSchedule::STATUS_DONE))?>
                        )</>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center bg-info">
                                <th>Key</th>
                                <th>Type</th>
                                <th title="Subtype">Subtype</th>
                                <?php foreach ($monthList as $month) : ?>
                                    <th style="font-size: 16px"><?= Html::encode($month)?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($scheduleTypeList) : ?>
                            <?php foreach ($scheduleTypeList as $item) : ?>
                            <tr class="text-center" title="<?= Html::encode($item->sst_title)?>">
                                <td title="Type Id: <?= $item->sst_id?>">
                                    <span class="label label-default"><?= Html::encode($item->sst_key)?></span>
                                </td>
                                <td class="text-left">
                                    <?= $item->getColorLabel()?> &nbsp;
                                    <?= $item->getIconLabel()?> &nbsp;
                                    <?= Html::encode($item->sst_name)?>
                                </td>
                                <td>
                                    <?php echo Html::encode($item->getSubtypeName()) ?>
                                    <?php /*if ($item->getSubtypeName()) :?>
                                        <i class="fa fa-check-circle"></i>
                                    <?php endif;*/ ?>
                                </td>
                                <?php foreach ($monthList as $monthId => $month) : ?>
                                    <?php /*echo $monthId; \yii\helpers\VarDumper::dump($scheduleSumData[$item->sst_id], 10, true)*/ ?>
                                    <td>
                                    <?php if (!empty($scheduleSumData[$item->sst_id][$monthId])) :
                                        $dataItem = $scheduleSumData[$item->sst_id][$monthId];

                                        if ($item->sst_subtype_id) {
                                            if (isset($subtypeTotalData[$item->sst_subtype_id][$monthId]['cnt'])) {
                                                $subtypeTotalData[$item->sst_subtype_id][$monthId]['duration'] += $dataItem['uss_duration'];
                                                $subtypeTotalData[$item->sst_subtype_id][$monthId]['cnt'] += $dataItem['uss_cnt'];
                                            } else {
                                                $subtypeTotalData[$item->sst_subtype_id][$monthId]['duration'] = $dataItem['uss_duration'];
                                                $subtypeTotalData[$item->sst_subtype_id][$monthId]['cnt'] = $dataItem['uss_cnt'];
                                            }
                                        }

                                        if (isset($scheduleTotalData[$monthId]['th'])) {
                                            $scheduleTotalData[$monthId]['th'] += $dataItem['uss_duration'];
                                            $scheduleTotalData[$monthId]['th_cnt'] += $dataItem['uss_cnt'];
                                        } else {
                                            $scheduleTotalData[$monthId]['th'] = $dataItem['uss_duration'];
                                            $scheduleTotalData[$monthId]['th_cnt'] = $dataItem['uss_cnt'];
                                        }
                                        ?>

                                            <?= round($dataItem['uss_duration'] / 60, 1)?>h
                                            / <?= Html::encode($dataItem['uss_cnt'])?>

                                    <?php else : ?>
                                        -
                                    <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6"></td>
                            </tr>

                            <?php if ($subtypeTotalData) : ?>
                                <?php foreach ($subtypeTotalData as $subtypeId => $dataItem) : ?>
                                    <tr class="text-center">
                                        <th></th>
                                        <th class="text-right">Total "<?php echo Html::encode(ShiftScheduleType::getSubtypeNameById($subtypeId))?>"</th>
                                        <th></th>
                                        <?php foreach ($monthList as $monthId => $month) : ?>
                                            <th>
                                                <?= isset($dataItem[$monthId]['duration']) ? round($dataItem[$monthId]['duration'] / 60, 1) . 'h' : '-'?> /
                                                <?= isset($dataItem[$monthId]['cnt']) ? ($dataItem[$monthId]['cnt']) : '-'?>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <tr class="text-center">
                                <th></th>
                                <th class="text-right">Total Hours</th>
                                <th></th>
                                <?php foreach ($monthList as $monthId => $month) : ?>
                                    <th>
                                        <?= isset($scheduleTotalData[$monthId]['th']) ? round($scheduleTotalData[$monthId]['th'] / 60, 1) . 'h' : '-'?> /
                                        <?= isset($scheduleTotalData[$monthId]['th_cnt']) ? ($scheduleTotalData[$monthId]['th_cnt']) : '-'?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>

                        </tfoot>
                    </table>


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
        locale: 'en',
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
        modal.find('.modal-body').html('');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            if (status === 'error') {
                alert(response);
            } else {
                modal.modal('show');
            }   
        });
    });
    

    function updateTimeLineList(startDate, endDate) 
    {
        $.pjax.reload({container: '#pjax-user-timeline', push: false, replace: false, timeout: 5000, data: {startDate: startDate, endDate: endDate}});
    }
    
    $('body').off('click', '.btn-open-timeline').on('click', '.btn-open-timeline', function (e) {
        e.preventDefault();
        let id = $(this).data('tl_id');
        openModalEventId(id);
    });
    
    
JS;

$this->registerJs($js);
