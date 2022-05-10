<?php

use modules\shiftSchedule\src\abac\ShiftAbacObject;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $resourceList array */
/* @var $groupIds array */

$this->title = 'Users Shift Calendar';
$this->params['breadcrumbs'][] = $this->title;
$bundle = \frontend\assets\UserShiftCalendarAsset::register($this);
?>

<div class="shift-schedule-calendar">
    <h1><i class="fa fa-calendar"></i> <?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE, Create user shift schedule event */
        if (\Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE)) :
            ?>
            <?= Html::a(
                '<i class="fa fa-plus-circle"></i> Add Schedule Event',
                '#',
                ['class' => 'btn btn-success', 'id' => 'btn-shift-event-add', 'title' => 'Add Schedule Event']
            ) ?>
        <?php endif; ?>
    </p>

    <div class="row">
        <div class="col-md-12">
            <div id="calendar" class="ssc"></div>
        </div>
    </div>
</div>

<div id="custom-event-tooltip-popup" class="md-tooltip">
    <div id="tooltip-event-header" class="md-tooltip-header">
        <span id="tooltip-event-name-age" class="md-tooltip-name-age"></span>
        <span id="tooltip-event-time" class="md-tooltip-time"></span>
    </div>
    <div class="md-tooltip-info">
        <div class="md-tooltip-title">
            Status: <span id="tooltip-event-title" class="md-tooltip-status md-tooltip-text"></span>
        </div>
        <div class="md-tooltip-title">Description: <span id="tooltip-event-description" class="md-tooltip-reason md-tooltip-text"></span></div>
        <?php if ($canDeleteEvent = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_DELETE)) : ?>
            <button id="tooltip-event-delete" mbsc-button data-color="danger" data-variant="outline" class="md-tooltip-delete-button">Delete appointment</button>
        <?php endif; ?>
    </div>
</div>

<?php
$ajaxUrl = Url::to(['shift-schedule/calendar-events-ajax']);
$resourceListJson = Json::encode($resourceList);
$today = date('Y-m-d', strtotime('+1 day'));
$modalUrl = Url::to(['/shift-schedule/add-event']);
$groupIdsJson = Json::encode($groupIds);
$formCreateSingleEventUrl = Url::to(['/shift-schedule/add-single-event']);
$deleteEventUrl = Url::to(['/shift-schedule/delete-event']);
$canCreateOnDoubleClick = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE_ON_DOUBLE_CLICK);
$js = <<<JS
var resourceListJson = $resourceListJson;
var groupIds = $groupIdsJson;
var calendarEventsAjaxUrl = '$ajaxUrl';
var today = '$today';
var modalUrl = '$modalUrl';
var events;
var canDeleteEvent = Boolean('$canDeleteEvent');
var canCreateOnDoubleClick = Boolean('$canCreateOnDoubleClick');

var formatDate = mobiscroll.util.datetime.formatDate;
var currentEvent;
var timer;
var \$tooltip = $('#custom-event-tooltip-popup');
if (canDeleteEvent) {
    var \$deleteButton = $('#tooltip-event-delete');
}
var \$header = $('#tooltip-event-header');
var \$data = $('#tooltip-event-name-age');
var \$time = $('#tooltip-event-time');
var \$status = $('#tooltip-event-title');
var \$description = $('#tooltip-event-description');

mobiscroll.setOptions({
    theme: 'ios',
    themeVariant: 'light'
});

mobiscroll.momentTimezone.moment = moment;

window.inst = $('#calendar').mobiscroll().eventcalendar({
        view: {
            timeline: { type: 'day', size: 2 },
            refDate: today
        },
        timeFormat: 'HH:mm',
        dataTimezone: 'utc',
        displayTimezone: 'local',
        //displayTimezone: 'Europe/Chisinau', //'local',
        timezonePlugin: mobiscroll.momentTimezone,
        clickToCreate: canCreateOnDoubleClick,
        dragToCreate: false,
        dragToMove: true,
        dragToResize: true,
//        renderScheduleEvent: function (data) {
//            let ev = data.original;
//            let color = data.color;
//            let icon = ev.extendedProps.icon;
//
//            return '<div class="ssc-event" style="border-color:' + color + ';background:' + color + '">' +
//                '<div class="ssc-event-cont">' +
//                '<span class="mbsc-icon ' + icon + '" style="background:' + color + '"></span>' +
//                '<span class="ssc-time" style="color:' + color + ';">' + data.start + '</span>' +
//                '<span class="ssc-title">' + ev.title + '</span></div></div>';
//        },
//         extendDefaultEvent: function () {
//             return {
//                 extendedProps: {icon: 'fa fa-folder'}
//             };
//         },
        resources: resourceListJson,
        
        //  renderResource: function (resource) {
        //     return '<div class="md-work-week-cont">' +
        //         '<div class="md-work-week-name">' + resource.name + '</div>' +
        //         '<div class="md-work-week-title">' + resource.title + '</div>' +
        //        // '<img class="md-work-week-avatar" src="' + resource.img + '"/>' +
        //         '</div>';
        // },
        renderHeader: function () {
            let str = '<div mbsc-calendar-nav class="md-work-week-nav"></div>' +
                '<div class="md-work-week-picker">' +
                '<label>Day (hours)<input mbsc-segmented type="radio" name="switching-timeline-view" value="day" class="md-timeline-view-change" checked></label>' +
                '<label>7 Days<input mbsc-segmented type="radio" name="switching-timeline-view" value="7day" class="md-timeline-view-change"></label>' +
                '<label>30 Days<input mbsc-segmented type="radio" name="switching-timeline-view" value="30days" class="md-timeline-view-change"></label>' +
                '<label>Week<input mbsc-segmented type="radio" name="switching-timeline-view" value="week" class="md-timeline-view-change"></label>' +
                '<label>Month<input mbsc-segmented type="radio" name="switching-timeline-view" value="month" class="md-timeline-view-change"></label>' +
                '<label>Month (day)<input mbsc-segmented type="radio" name="switching-timeline-view" value="month-day" class="md-timeline-view-change"></label>' +
                '</div>' +
                '<div mbsc-calendar-prev class="md-work-week-prev"></div>' +
                '<div mbsc-calendar-today class="md-work-week-today"></div>' +
                '<div mbsc-calendar-next class="md-work-week-next"></div>';
            return str;
        },
        
        onPageLoading: function (event, inst) {
            let year = event.firstDay.getUTCFullYear(),
                month = event.firstDay.getUTCMonth() + 1,
                day = event.firstDay.getUTCDate();
            
            let endYear = event.lastDay.getUTCFullYear(),
                endMonth = event.lastDay.getUTCMonth() + 1,
                endDay = event.lastDay.getUTCDate();
            
            //alert(event.lastDay.getUTCDay());
            
            let startDate = year + '-' + month + '-' + day;
            let endDate = endYear + '-' + endMonth + '-' + endDay;
            
            getCalendarEvents(startDate, endDate, groupIds);
        },
        
        
         onEventCreate: function (args, inst) {
            // store temporary event
            createUpdateEvent(args.event, true);
            /*tempMeal = args.event;
            setTimeout(function () {
                addMealPopup();
            }, 100);*/
        },
        
        onEventClick: function (args, inst) {
            var event = args.event;
            // var resource = events.find(function (e) {return e.resource === event.resource});
            let startDate = new Date(event.start);
            let endDate = new Date(event.end);
            var time = formatDate('YYYY-MM-DD H:mm', startDate) + ' - ' + formatDate('YYYY-MM-DD H:mm', endDate);
            var button = {};

            currentEvent = event;
            console.log(event);

            if (event.confirmed) {
                button.text = 'Cancel appointment';
                button.type = 'warning';
            } else {
                button.text = 'Confirm appointment';
                button.type = 'success';
            }
            
            \$header.css('background-color', event.borderColor || event.color);
            \$data.text(event.title);
            \$time.text(time);

            \$status.text(event.status);
            \$description.text(event.description);

            clearTimeout(timer);
            timer = null;

            tooltip.setOptions({ anchor: args.domEvent.target });
            tooltip.open();
        }
        // onEventClick: function (args, inst) {
            // oldMeal = $.extend({}, args.event);
            // tempMeal = args.event;
            //
            // //if (!popup.isVisible()) {
            //     //editMealPopup(args);
            //     console.log(args);
            // //}
        // },
        
        // onEventCreated: function () {
        //     mobiscroll.toast({
        //         message: 'Event created'
        //     });
        // },
        // onEventUpdated: function () {
        //     mobiscroll.toast({
        //         message: 'Event updated'
        //     });
        // },
        // onEventCreateFailed: function (event) {
        //     mobiscroll.toast({
        //         message: 'Can\'t create event'
        //     });
        // },
        // onEventUpdateFailed: function (event) {
        //     mobiscroll.toast({
        //         message: 'Can\'t move event'
        //     });
        // }
        
    }).mobiscroll('getInst');



    function createUpdateEvent(event, isNew) {
            if (isNew) {
                let userId;
                if (event.resource.indexOf('us-') === 0) {
                    userId = event.resource.substring(3);
                }
                let eventStartDate = new Date(event.start);
                let [year, month, day, hour, minute] = [eventStartDate.getFullYear(), eventStartDate.getMonth()+1, eventStartDate.getDate(), eventStartDate.getHours(), eventStartDate.getMinutes(), eventStartDate];
                let startDate = year + '-' + month + '-' + day + ' ' + hour + ':' + minute;
                let modal = $('#modal-md');
                modal.find('.modal-title').html('Add event for user ');
                modal.on('hide.bs.modal', function (e) {
                    inst.removeEvent(event);
                });
                modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
                $.get('$formCreateSingleEventUrl?userId=' + userId + '&startDate=' + startDate, function(data) {
                    modal.find('.modal-body').html(data);
                }).fail(function (xhr) {
                    setTimeout(function () {
                        modal.modal('hide');
                        createNotify('Error', xhr.statusText, 'error');
                    }, 800);
                })
            }
        }


    $('.md-timeline-view-change').change(function (ev) {
        switch (ev.target.value) {
            case 'day':
                inst.setOptions({
                    view: {
                        timeline: { type: 'day', size: 2 },
                        refDate: today
                    }
                })
                break;
            case 'month':
                inst.setOptions({
                    view: {
                        timeline: { type: 'month', timeCellStep: 360, timeLabelStep: 360 }
                    }
                })
                break;
                
            case '7day':
                inst.setOptions({
                    view: {
                        timeline: { type: 'day', timeCellStep: 360, timeLabelStep: 360, size: 7 }
                    },
                    refDate: today
                })
                break;
                
            case '30days':
                inst.setOptions({
                    view: {
                        timeline: { type: 'day', timeCellStep: 720, timeLabelStep: 720, size: 30 }
                    },
                    refDate: today
                })
                break;
                
            case 'week':
                inst.setOptions({
                    view: {
                        timeline: { type: 'week', timeCellStep: 720, timeLabelStep: 720 }
                    }/*,
                    refDate: today*/
                })
                break;
            
            case 'month-day':
                inst.setOptions({
                    view: {
                        timeline: { type: 'month', timeCellStep: 1440, timeLabelStep: 1440 }
                    }
                })
                break;
        }
    });
    
    var tooltip = \$tooltip.mobiscroll().popup({
        display: 'anchored',
        touchUi: false,
        showOverlay: false,
        contentPadding: false,
        closeOnOverlayClick: false,
        width: 350
    }).mobiscroll('getInst');

    \$tooltip.mouseenter(function (ev) {
        if (timer) {
            clearTimeout(timer);
            timer = null;
        }
    });

    \$tooltip.mouseleave(function (ev) {
        timer = setTimeout(function () {
            tooltip.close();
        }, 200);
    });

    if (canDeleteEvent) {
        \$deleteButton.on('click', function (ev) {
            mobiscroll.confirm({
                title: 'Are you sure you want to delete shift?',
                // message: 'It looks like someone from the team won\'t be able to join the meeting.',
                okText: 'Yes',
                cancelText: 'No',
                callback: function (res) {
                    if (res) {
                        $.ajax({
                            url: '$deleteEventUrl',
                            data: {'shiftId': currentEvent.id},
                            type: 'post',
                            cache: false,
                            dataType: 'json',
                            success: function (data) {
                                if (data.error) {
                                    createNotify('Error', data.message, 'error');
                                } else {
                                    inst.removeEvent(currentEvent);
                                    tooltip.close();
                                    createNotify('Success', data.message, 'success');
                                }
                            },
                            error: function (xhr) {
                                createNotify('Error', xhr.responseText, 'error');
                            }
                        })
                    }
                }
            });
    
            // mobiscroll.toast({
            //     message: 'Appointment deleted'
            // });
        });
    }



    function getCalendarEvents(date, endDate, groups) {
         let params = groups.join(',');
         $.getJSON(calendarEventsAjaxUrl + '?start=' + date + '&end=' + endDate + '&callback&groups=' + params, function (data) {
                inst.resources = data.resources;
                setTimelineEvents(data.data);
        
                mobiscroll.toast({
                    message: 'New events loaded'
                });
            }, 'jsonp');
    }
    
    window.setTimelineEvents = function (data)
    {
        window.inst.setEvents(data);
        events = data;
    }
    
    window.addTimelineEvent = function (data) {
        window.inst.addEvent(data);
        events.push(data);
    } 
    // $.getJSON(calendarEventsAjaxUrl, function (events) {
    //     inst.setEvents(events);
    // }, 'jsonp');
    
    $('#btn-shift-event-add').on('click', function (e) {
        e.preventDefault(); 
        let calendarStartDt = window,i
        let title = $(this).attr('title');
        let modal = $('#modal-md');
        modal.find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        modal.find('.modal-title').html(title);
        modal.find('.modal-body').load(modalUrl, {}, function( response, status, xhr ) {
            if (status == 'error') {
                createNotifyByObject({
                    'title': 'Error',
                    'type': 'error',
                    'text': xhr.statusText
                })
            } else {
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
    });

JS;

$this->registerJs($js);
