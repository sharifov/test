<?php

/**
 * @var View $this
 * @var Employee $user
 * @var string $userTimeZone
 * @var ActiveDataProvider $dataProvider
 * @var ShiftScheduleRequestSearch $searchModel
 * @var ActiveDataProvider $dataProviderAll
 * @var ShiftScheduleRequestSearch $searchModelAll
 */

use common\models\Employee;
use frontend\assets\FullCalendarAsset;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

$this->title = 'User Shift Schedule Requests';
$this->params['breadcrumbs'][] = $this->title;
$bundle = FullCalendarAsset::register($this);

?>

    <div class="shift-schedule-request-index">
        <h1><i class="fa fa-calendar"></i> <?= Html::encode($this->title) ?></h1>

        <div class="row">
            <div class="col-md-6">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>
                            <i class="fa fa-calendar"></i> Schedule Request Calendar
                            (TimeZone: <?= Html::encode($userTimeZone) ?>)
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
                <?php Pjax::begin([
                    'id' => 'pjax-pending-requests',
                    'enablePushState' => false,
                    'enableReplaceState' => false,
                ]); ?>
                <?= $this->render('partial/_pending_requests', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]) ?>
                <?php Pjax::end(); ?>
            </div>
        </div>

        <?php Pjax::begin([
            'id' => 'pjax-all-requests',
            'enablePushState' => false,
            'enableReplaceState' => false,
        ]); ?>
        <?= $this->render('partial/_all_requests', [
            'searchModelAll' => $searchModelAll,
            'dataProviderAll' => $dataProviderAll,
        ]) ?>
        <?php Pjax::end(); ?>

    </div>

<?php
$ajaxUrl = Url::to(['user-shift-schedule-request/my-data-ajax', 'start' => date('Y-m-d'), 'end' => date('Y-m-d', strtotime('+100 days'))]);
$openModalEventUrl = Url::to(['user-shift-schedule-request/get-event']);

$js = <<<JS
var shiftScheduleDataUrl = '$ajaxUrl';
var openModalEventUrl = '$openModalEventUrl';
var calendarEl = document.getElementById('calendar');
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
        if (info.event.extendedProps.backgroundImage && info.event.extendedProps.backgroundImage.length > 0) {
            $(info.el).css('background-image', info.event.extendedProps.backgroundImage);
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
    selectable: false,
    eventClick: function(info) {
        info.jsEvent.preventDefault();
        var eventObj = info.event;
        openModalEventId(eventObj.id);
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

function updatePendingRequests() 
{
    $.pjax.reload({container: '#pjax-pending-requests', push: false, replace: false, timeout: 5000, async:false});
}

function updateAllRequests()
{
    $.pjax.reload({container: '#pjax-all-requests', push: false, replace: false, timeout: 5000, async:false})
}

$(document).on('RequestDecision:response', function (e, params) {
    if (params.requestStatus) {
        calendar.refetchEvents();
        updateAllRequests();
        updatePendingRequests();
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
