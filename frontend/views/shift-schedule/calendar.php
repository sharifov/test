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
/* @var $resourceList array */


$this->title = 'Calendar Mobiscroll';
$this->params['breadcrumbs'][] = $this->title;

$bundle = \frontend\assets\MobiscrollCalendarAsset::register($this);

?>
<style>
    .tl-tpl .mbsc-schedule-event.mbsc-ltr {
        height: auto !important;
    }

    .tl-tpl-event {
        border: 1px solid transparent;
        margin: 2px 0;
    }

    .tl-tpl-event-cont {
        background: rgba(255, 255, 255, .8);
        font-size: 15px;
        height: 32px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .tl-tpl-event-cont .mbsc-icon {
        padding:  7px 6px 2px 5px;
        box-sizing: content-box;
    }

    .mbsc-timeline-event-start .tl-tpl-event,
    .mbsc-timeline-event-start .tl-tpl-event-cont,
    .mbsc-timeline-event-start .tl-tpl-event-cont .mbsc-icon {
        border-top-left-radius: 20px;
        border-bottom-left-radius: 20px;
    }

    .mbsc-timeline-event-end .tl-tpl-event,
    .mbsc-timeline-event-end .tl-tpl-event-cont,
    .mbsc-timeline-event-end .tl-tpl-event-cont .mbsc-icon {
        border-top-right-radius: 20px;
        border-bottom-right-radius: 20px;
    }

    .tl-tpl-event-cont .mbsc-icon:before {
        color: #fff;
        font-size: 15px;
    }

    .tl-tpl-time {
        margin: 0 10px;
    }

    .tl-tpl-title {
        color: #666;
    }

    .tl-tpl .mbsc-timeline-column,
    .tl-tpl .mbsc-timeline-header-column {
        min-width: 100px;
    }

    .tl-tpl .mbsc-timeline-resource,
    .tl-tpl .mbsc-timeline-row {
        min-height: 100px;
    }










    .md-work-week-cont {
        position: relative;
        padding-left: 50px;
    }

    .md-work-week-avatar {
        position: absolute;
        max-height: 50px;
        max-width: 50px;
        top: 21px;
        -webkit-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
        left: 20px;
    }

    .md-work-week-name {
        font-size: 16px;
    }

    .md-work-week-title {
        font-size: 12px;
        margin-top: 5px;
    }

    .tl-tpl .mbsc-segmented {
        max-width: 600px;
        margin: 0 auto;
        padding: 1px;
    }

    .md-work-week-picker {
        flex: 1 0 auto;
    }

    .md-work-week-nav {
        width: 200px;
    }

    .tl-tpl .mbsc-timeline-resource {
        display: flex;
        align-items: center;
    }

    .tl-tpl .mbsc-timeline-resource-col {
        width: 205px;
    }

    @supports (overflow:clip) {
        .tl-tpl.mbsc-ltr .mbsc-schedule-event-inner {
            left: 205px;
        }
        .tl-tpl.mbsc-rtl .mbsc-schedule-event-inner {
            right: 205px;
        }
    }





    .md-custom-range-view-controls {
        display: flex;
        flex: 1 0 auto;
        justify-content: end;
        align-items: center;
    }

    .mbsc-material .mbsc-calendar-title {
        font-size: 1.428572em;
        font-weight: 400;
        text-transform: none;
        line-height: 1.4em;
    }


</style>

<div class="shift-schedule-calendar">
    <h1><i class="fa fa-calendar"></i> <?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-12">
            <div id="calendar"  class="tl-tpl"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div id="eventcalendar"></div>
        </div>
        <div class="col-md-12">
            <div id="mycalendar"></div>
        </div>
    </div>
</div>


<?php
$ajaxUrl = \yii\helpers\Url::to(['shift-schedule/calendar-events-ajax']);
$resourceListJson = \yii\helpers\Json::encode($resourceList);
$today = date('Y-m-d');

$js = <<<JS
var resourceListJson = $resourceListJson;
var calendarEventsAjaxUrl = '$ajaxUrl';
var today = '$today';

// $('#eventcalendar').mobiscroll().eventcalendar({
//     data: [{
//         start: new Date(),
//         title: 'Today\'s event'
//     }, {
//         start: new Date(2022, 4, 19, 9, 0),
//         end: new Date(2022, 4, 20, 13, 0),
//         title: 'Multi day event'
//     }]
// });


mobiscroll.setOptions({
    theme: 'ios',
    themeVariant: 'light'
});

mobiscroll.momentTimezone.moment = moment;

var inst = $('#calendar').mobiscroll().eventcalendar({
        view: {
            timeline: { type: 'day', size: 2 },
            refDate: today
        },
        timeFormat: 'HH:mm',
        dataTimezone: 'utc',
        displayTimezone: 'local',
        //displayTimezone: 'Europe/Chisinau', //'local',
        timezonePlugin: mobiscroll.momentTimezone,
        //clickToCreate: true,
        dragToCreate: true,
        dragToMove: true,
        dragToResize: true,
//        renderScheduleEvent: function (data) {
//            let ev = data.original;
//            let color = data.color;
//            let icon = ev.extendedProps.icon;
//
//            return '<div class="tl-tpl-event" style="border-color:' + color + ';background:' + color + '">' +
//                '<div class="tl-tpl-event-cont">' +
//                '<span class="mbsc-icon ' + icon + '" style="background:' + color + '"></span>' +
//                '<span class="tl-tpl-time" style="color:' + color + ';">' + data.start + '</span>' +
//                '<span class="tl-tpl-title">' + ev.title + '</span></div></div>';
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
            
            getCalendarEvents(startDate, endDate);
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
            oldMeal = $.extend({}, args.event);
            tempMeal = args.event;

            //if (!popup.isVisible()) {
                //editMealPopup(args);
                console.log(args);
            //}
        },
        
        onEventCreated: function () {
            mobiscroll.toast({
                message: 'Event created'
            });
        },
        onEventUpdated: function () {
            mobiscroll.toast({
                message: 'Event updated'
            });
        },
        onEventCreateFailed: function (event) {
            mobiscroll.toast({
                message: 'Can\'t create event'
            });
        },
        onEventUpdateFailed: function (event) {
            mobiscroll.toast({
                message: 'Can\'t move event'
            });
        }
        
    }).mobiscroll('getInst');



    function createUpdateEvent(event, isNew) {
            mobiscroll.confirm({
                title: 'Are you sure you want to proceed?',
                message: 'It looks like someone from the team won\'t be able to join the meeting.',
                okText: 'Yes',
                cancelText: 'No',
                callback: function (res) {
                    if (res) {
                        if (isNew) {
                            calendar.addEvent(event);
                        } else {
                            calendar.updateEvent(event);
                        }
    
                        mobiscroll.toast({
                            message: isNew ? 'Event created' : 'Event updated'
                        });
                    }
                }
            });
        }


    $('.md-timeline-view-change').change(function (ev) {
            switch (ev.target.value) {
                case 'day':
                    inst.setOptions({
                        view: {
                            timeline: { 
                                type: 'day',
                                // timeCellStep: 60,
                                // timeLabelStep: 60,
                                //eventList: true
                                size: 2
                            },
                            refDate: today
                        }
                    })
                    break;
                case 'month':
                    inst.setOptions({
                        view: {
                            timeline: {
                                type: 'month',
                                // startDay: 1,
                                // endDay: 5,
                                // eventList: true,
                                // weekNumbers: false,
                                timeCellStep: 360,
                                timeLabelStep: 360
                            }
                        }
                    })
                    break;
                    
                case '7day':
                    inst.setOptions({
                        view: {
                            timeline: {
                                type: 'day',
                                timeCellStep: 360,
                                timeLabelStep: 360,
                                size: 7
                                /*eventList: true,
                                startDay: 1,
                                endDay: 5*/
                                //startDay: 1
                                // timeCellStep: 360,
                                // timeLabelStep: 360,
                                // eventList: true
                            }
                        },
                        refDate: today
                    })
                    break;
                    
                case '30days':
                    inst.setOptions({
                        view: {
                            timeline: {
                                type: 'day',
                                timeCellStep: 720,
                                timeLabelStep: 720,
                                size: 30,
                                //eventList: true,
                                /*startDay: 1,
                                endDay: 5*/
                                //startDay: 1
                                // timeCellStep: 360,
                                // timeLabelStep: 360,
                                // eventList: true
                            }
                        },
                        refDate: today
                    })
                    break;
                    
                case 'week':
                    inst.setOptions({
                        view: {
                            timeline: {
                                type: 'week',
                                /*eventList: true,
                                startDay: 1,
                                endDay: 5*/
                                //startDay: 1
                                timeCellStep: 720,
                                timeLabelStep: 720
                                // eventList: true
                            }
                        }/*,
                        refDate: today*/
                    })
                    break;
                
                case 'month-day':
                    inst.setOptions({
                        view: {
                            timeline: {
                                type: 'month',
                                // startDay: 1,
                                // endDay: 5,
                                // eventList: true,
                                // weekNumbers: false,
                                timeCellStep: 1440,
                                timeLabelStep: 1440
                            }
                        }
                    })
                    break;
            }
        });



    function getCalendarEvents(date, endDate) {
         $.getJSON(calendarEventsAjaxUrl + '?start=' + date + '&end=' + endDate + '&callback', function (data) {
                // var events = [];
                //
                // for (var i = 0; i < data.length; i++) {
                //     var event = data[i];
                //     events.push({
                //         start: event.start,
                //         end: event.end,
                //         title: event.title,
                //         resource: event.resource
                //     });
                // }
        
                inst.resources = data.resources;
                inst.setEvents(data.data);
        
                mobiscroll.toast({
                    message: 'New events loaded'
                });
            }, 'jsonp');
    }
    // $.getJSON(calendarEventsAjaxUrl, function (events) {
    //     inst.setEvents(events);
    // }, 'jsonp');

JS;

$this->registerJs($js);
