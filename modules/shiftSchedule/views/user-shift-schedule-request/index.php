<?php

/**
 * @var View $this
 * @var Employee $user
 * @var string $userTimeZone
 * @var ActiveDataProvider $dataProvider
 * @var ShiftScheduleRequestSearch $searchModel
 */

use common\components\grid\DateTimeColumn;
use common\models\Employee;
use frontend\assets\FullCalendarAsset;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;
use yii\grid\GridView;

$this->title = Yii::t('schedule-request', 'User Shift Schedule Requests');
$this->params['breadcrumbs'][] = $this->title;
$bundle = FullCalendarAsset::register($this);
$shiftScheduleTypes = ShiftScheduleType::getList(true);

?>

    <div class="shift-schedule-request-index">
        <h1><i class="fa fa-calendar"></i> <?= Html::encode($this->title) ?></h1>

        <div class="row">
            <div class="col-md-6">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>
                            <?= Yii::t(
                                'schedule-request',
                                '{icon} Schedule Request Calendar (TimeZone: {userTimeZone})',
                                [
                                    'icon' => '<i class="fa fa-calendar"></i>',
                                    'userTimeZone' => Html::encode($userTimeZone),
                                ]
                            ) ?>
                        </h2>
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
                <?php Pjax::begin(['id' => 'pjax-user-timeline']); ?>
                <div class="x_panel">
                    <div class="x_title">
                        <h2>
                            <?= Yii::t(
                                'schedule-request',
                                '{icon} TimeLine Schedule Request List ({clientStartDate} - {clientEndDate})',
                                [
                                    'icon' => '<i class="fa fa-bars"></i>',
                                    'clientStartDate' => Html::encode($searchModel->clientStartDate),
                                    'clientEndDate' => Html::encode($searchModel->clientEndDate),
                                ]
                            ) ?>
                        </h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                [
                                    'attribute' => 'srh_uss_id',
                                    'options' => [

                                    ],
                                    'value' => function (ShiftScheduleRequestSearch $model) {
                                        return $model->srhCreatedUser->nickname;
                                    },
                                    'label' => Yii::t('schedule-request', 'User'),
                                    // @todo: 'filter' => 'user group'
                                ],
                                [
                                    'label' => 'Type',
                                    'value' => static function (ShiftScheduleRequestSearch $model) {
                                        return $model->srhSst ? $model->srhSst->getColorLabel() : '-';
                                    },
                                    'format' => 'raw',

                                ],
                                [
                                    'attribute' => 'srh_sst_id',
                                    'value' => function (ShiftScheduleRequestSearch $model) {
                                        return Html::a(
                                            $model->getScheduleTypeTitle() ?? $model->srh_sst_id,
                                            null,
                                            ['class' => 'btn-open-timeline', 'data-tl_id' => $model->srh_id]
                                        );
                                    },
                                    'options' => [
                                        'style' => 'width: 20%',
                                    ],
                                    'label' => Yii::t('schedule-request', 'Schedule Type'),
                                    'filter' => $shiftScheduleTypes,
                                    'format' => 'raw',
                                ],
                                [
                                    'label' => Yii::t('schedule-request', 'Start Date Time'),
                                    'class' => DateTimeColumn::class,
                                    'attribute' => 'srh_start_utc_dt',
                                    'format' => 'byUserDateTime',
                                    'filter' => false
                                ],
                                [
                                    'label' => Yii::t('schedule-request', 'End Date Time'),
                                    'class' => DateTimeColumn::class,
                                    'attribute' => 'srh_end_utc_dt',
                                    'format' => 'byUserDateTime',
                                    'filter' => false
                                ],
                                [
                                    'label' => Yii::t('schedule-request', 'Status'),
                                    'attribute' => 'srh_status_id',
                                    'value' => function (ShiftScheduleRequestSearch $model) {
                                        $statusName = $model->getStatusName();
                                        if (!empty($statusName)) {
                                            return sprintf(
                                                '<span class="badge badge-%s">%s</span>',
                                                $model->getStatusNameColor(),
                                                $statusName
                                            );
                                        }
                                        return $model->srh_status_id;
                                    },
                                    'options' => [
                                        'style' => 'width: 10%',
                                    ],
                                    'filter' => ShiftScheduleRequest::getList(),
                                    'format' => 'raw',
                                ],
                            ],
                        ]) ?>
                    </div>
                </div>
                <?php Pjax::end(); ?>
            </div>

        </div>
    </div>

<?php
$ajaxUrl = Url::to(['user-shift-schedule-request/my-data-ajax', 'start' => date('Y-m-d'), 'end' => date('Y-m-d', strtotime('+100 days'))]);
$openModalEventUrl = Url::to(['user-shift-schedule-request/get-event']);

$js = <<<JS
var shiftScheduleDataUrl = '$ajaxUrl';
var openModalEventUrl = '$openModalEventUrl';
var calendarEl = document.getElementById('calendar');
var selectedRange = {};
var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height: 800,
    navLinks: true,
    displayEventEnd: true,
    eventTimeFormat: { // like '14:30:00'
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    },
    eventDidMount: function(info) {
        if (info.event.extendedProps.icon.length > 0) {
            $(info.el).find('.fc-event-title').prepend('<i class="' + info.event.extendedProps.icon + '"></i> ');
        }
        if (info.event.extendedProps.description.length > 0) {
            $(info.el).tooltip({ "title": info.event.extendedProps.description});
        }
    },
    navLinkWeekClick: function(weekStart, jsEvent) {
        console.log('week start', weekStart.toISOString());
        console.log('coords', jsEvent.pageX, jsEvent.pageY);
    },
    firstDay: 1,
    headerToolbar: {
        left: 'prev,next today',
        center: 'title', // buttons for switching between views
        right: 'dayGridMonth,customDayGridWeek,timeGridWeek,timeGridDay' //,listDay,listWeek,listMonth
    },
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
        customDayGridWeek: {
            type: 'dayGridWeek',
            buttonText: 'WeekDay'
        }
    },
    timeZone: '$userTimeZone',
    locale: 'en',
    dayMaxEvents: true, // allow "more" link when too many events
    eventSources: [
        // your event source
        {
            url: shiftScheduleDataUrl,
            method: 'GET',
            failure: function() {
                alert('There was an error while fetching events!');
              }
            
        }
    ],
      //events: 'https://fullcalendar.io/api/demo-feeds/events.json',
    editable: false,
    selectable: true,
    eventClick: function(info) {
        info.jsEvent.preventDefault();
        var eventObj = info.event;
        openModalEventId(eventObj.id);
    },
    select: function(info) {
        updateTimeLineList(info.startStr, info.endStr);
        selectedRange = {
            start: info.startStr,
            end: info.endStr
        }
    }
});

calendar.render();

function openModalEventId(id)
{
    let modal = $('#modal-md');
    let eventUrl = openModalEventUrl + '?id=' + id;
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

function updateTimeLineList(startDate, endDate) 
{
    $.pjax.reload({container: '#pjax-user-timeline', push: false, replace: false, timeout: 5000, data: {startDate: startDate, endDate: endDate}});
}

$(document).on('RequestDecision:response', function (e, params) {
    if (params.requestStatus) {
        calendar.refetchEvents();
        updateTimeLineList(selectedRange.start, selectedRange.end);
        $('#modal-md').modal('hide');
    }
});

$('body').off('click', '.btn-open-timeline').on('click', '.btn-open-timeline', function (e) {
    e.preventDefault();
    let id = $(this).data('tl_id');
    openModalEventId(id);
});

JS;

$this->registerJs($js);
