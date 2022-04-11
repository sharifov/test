<?php

use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $monthList array */
/* @var $scheduleTypeList ShiftScheduleType[] */
/* @var $scheduleSumData array */

$this->title = 'My Shift Schedule';
$this->params['breadcrumbs'][] = $this->title;

$bundle = \frontend\assets\FullCalendarAsset::register($this);
?>
<div class="shift-schedule-index">

    <h1><i class="fa fa-calendar"></i> <?= Html::encode($this->title) ?></h1>


    <div class="row">
        <div class="col-md-6">
            <div class="x_panel">
                <div class="x_title">
                    <h2><i class="fa fa-calendar"></i> My Shift Schedule</h2>
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
                    <h2><i class="fa fa-list"></i> Stats by Months</h2>
                    <!--            <ul class="nav navbar-right panel_toolbox" style="min-width: initial;">-->
                    <!--                <li>-->
                    <!--                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>-->
                    <!--                </li>-->
                    <!--            </ul>-->
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" style="display: block">
                    <div class="row">

                        <table class="table table-bordered">
                            <thead>
                            <tr class="text-center">
                                <th></th>
                                <th></th>
                                <?php foreach ($monthList as $month) : ?>
                                    <th><h4><?= Html::encode($month)?></h4></th>
                                <?php endforeach; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if ($scheduleTypeList) : ?>
                                <?php foreach ($scheduleTypeList as $item) : ?>
                                <tr class="text-center" title="<?= Html::encode($item->sst_title)?>">
                                    <td class="text-left">
                                        <span class="label label-default"><?= Html::encode($item->sst_key)?></span>
                                    </td>
                                    <td class="text-left">
                                        <?= $item->getColorLabel()?>
                                        <?= Html::encode($item->sst_name)?>
                                    </td>
                                    <?php foreach ($monthList as $monthId => $month) : ?>
                                        <?php if (!empty($scheduleSumData[$item->sst_id][$monthId])) : ?>
                                            <?php foreach ($scheduleSumData[$item->sst_id][$monthId] as $dataItem) : ?>
                                            <td><?= Html::encode($dataItem['uss_sst_id'])?></td>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>




    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /*= Html::a('Create Task', ['create'], ['class' => 'btn btn-success'])*/ ?>
    </p>

    <?php /*= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            't_id',
            't_key',
            't_name',
            [
                'attribute' => 't_category_id',
                'value' => function (\common\models\Task $model) {
                    return $model->getCategoryName();
                },
                'filter' => \common\models\Task::CAT_LIST
            ],

            't_description',
            't_hidden:boolean',
            't_sort_order',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); */?>
    <?php Pjax::end(); ?>
</div>

<?php
$js = <<<JS
    var calendarEl = document.getElementById('calendar');


    var calendar = new FullCalendar.Calendar(calendarEl, {
        //initialView: 'dayGridWeek',
        initialView: 'dayGridMonth',
        height: 800,
        navLinks: true,
        
        // initialDate: '2022-04-05',
        // eventColor: 'green',

      // eventDidMount: function(info) {
      //   var tooltip = new Tooltip(info.el, {
      //     title: info.event.extendedProps.description,
      //     placement: 'top',
      //     trigger: 'hover',
      //     container: 'body'
      //   });
      // },

        
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
      
      views: {
        // customTimeGridDay: {
        //   type: 'timeGrid',
        //   duration: { days: 7 },
        //   buttonText: '7 days'
        // },
        customDayGridWeek: {
          type: 'dayGridWeek',
          buttonText: 'WeekDay'
        },
        
        listDay: { buttonText: 'list day' },
        listWeek: { buttonText: 'list week' },
        listMonth: { buttonText: 'list month' }
      },
      
      //plugins: [ 'dayGridPlugin' ],
        timeZone: 'local',
        locale: 'en',
        dayMaxEvents: true, // allow "more" link when too many events
        events: 'https://fullcalendar.io/api/demo-feeds/events.json?overload-day',
        
          //events: 'https://fullcalendar.io/api/demo-feeds/events.json',
          editable: false,
          selectable: true,
        
        //events: 'https://fullcalendar.io/api/demo-feeds/events.json?with-resources=2',
        // events: [
        //     { start: '2022-04-06T12:30:00Z' }, // will be shifted to local
        //     { start: '2022-04-07T12:30:00' }, // already same offset as local, so won't shift
        //     { start: '2022-04-08T12:30:00' } // will be parsed as if it were '2018-09-01T12:30:00+XX:XX'
        //   ],
          dateClick: function(arg) {
            
            // console.log('Clicked on: ' + arg.dateStr);
            // console.log('Coordinates: ' + arg.jsEvent.pageX + ',' + arg.jsEvent.pageY);
            // console.log('Current view: ' + arg.view.type);
            //console.log('Resource ID: ' + arg.resource.id);
            console.log(arg);
            // change the day's background color just for fun
            // arg.dayEl.style.backgroundColor = 'red';
            
            //console.log(arg.date.toString()); // use *local* methods on the native Date Object
            // will output something like 'Sat Sep 01 2018 00:00:00 GMT-XX:XX (Eastern Daylight Time)'
          },
          select: function(info) {
            console.log(info);
            //console.log('selected ' + info.startStr + ' to ' + info.endStr);
          }

    });

    // var calendar = new FullCalendar.Calendar(calendarEl, {
    //     initialView: 'dayGridMonth'
    // });
    calendar.render();
JS;

$this->registerJs($js);
