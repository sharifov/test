<?php

use frontend\assets\UserShiftCalendarAsset;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\TimelineCalendarFilter;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $timelineCalendarFilter TimelineCalendarFilter */
/* @var $userGroups array */

$this->title = 'Users Shift Calendar';
$this->params['breadcrumbs'][] = $this->title;
$bundle = UserShiftCalendarAsset::register($this);
?>


<div class="shift-schedule-calendar">
    <h1><i class="fa fa-calendar"></i> <?= Html::encode($this->title) ?></h1>

    <?= $this->render('partial/_filter_form', [
        'timelineCalendarFilter' => $timelineCalendarFilter,
        'userGroups' => $userGroups
    ]) ?>

    <?php if (!empty($timelineCalendarFilter->userGroups)) : ?>
        <?php
        /** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE, Create user shift schedule event */
        if (\Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE)) :
            ?>
            <?= Html::a(
                '<i class="fa fa-plus-circle"></i> Add Schedule Event',
                null,
                ['class' => 'btn btn-success btn-sm', 'id' => 'btn-shift-event-add', 'title' => 'Add Schedule Event']
            ) ?>
        <?php endif; ?>

        <?php
        $canDelete = Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_MULTIPLE_DELETE_EVENTS);
        $canUpdate = Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_MULTIPLE_UPDATE_EVENTS);
        if ($canDelete || $canUpdate) : ?>
            <?= Html::a('<i class="fas fa-th-large"></i> Multiple Manage Mode', null, ['id' => 'multiple-manage-mode-btn', 'class' => 'btn btn-warning btn-sm']) ?>
            <?= Html::a('<i class="fas fa-times-circle"></i> Exit Mode', null, [ 'class' => 'btn btn-danger btn-sm', 'id' => 'btn-multiple-exit-mode', 'style' => 'display: none;']) ?>
            <div class="btn-group" id="check_uncheck_btns" style="display: none; margin-bottom: 4px; height: 28px; margin-left: 7px;">
                <?php echo Html::button('<span class="fa fa-square-o"></span> Select All', ['class' => 'btn btn-sm btn-default', 'id' => 'btn-check-all']); ?>

                <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu">
                    <p>
                        <?php if ($canUpdate) : ?>
                            <?= Html::a('<i class="fa fa-edit text-warning"></i> Multiple update', null, [
                                'class' => 'dropdown-item btn-multiple-update-events',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal-md'
                            ])
                            ?>
                        <?php endif; ?>
                        <?php if ($canDelete) : ?>
                            <?= Html::a('<i class="fas fa-trash-alt text-danger"></i> Delete Events', null, [
                                'class' => 'dropdown-item btn-multiple-delete-events',
                                'data' => [
                                    'url' => Url::to(['shift-schedule/multiple-delete']),
                                    'title' => 'Delete Events',
                                ],
                            ])
                            ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

    <div class="row">
        <div class="col-md-12" id="calendar-wrapper">
            <div id="calendar" class="ssc"></div>
        </div>
    </div>
    <?php else : ?>
        <?= \yii\bootstrap4\Alert::widget([
            'options' => [
                'class' => 'alert-warning',
            ],
            'body' => 'You dont have an associated group. In this case, you cannot view calendar events',
        ]) ?>
    <?php endif; ?>

</div>

<div id="custom-event-tooltip-popup" class="md-tooltip">
    <div id="tooltip-event-header" class="md-tooltip-header">
        <span id="tooltip-event-name-age" class="md-tooltip-name-age"></span>
    </div>
    <div class="md-tooltip-info">
        <div class="md-tooltip-title">
          Title: <span id="tooltip-event-title" class="md-tooltip-title md-tooltip-text"></span>
        </div>
        <div class="md-tooltip-title">
            Status: <span id="tooltip-event-status" class="md-tooltip-status md-tooltip-text"></span>
        </div>
        <div class="md-tooltip-title">Event Range: <span id="tooltip-event-time" class="md-tooltip-text"></span></div>
        <button id="tooltip-event-view" class="btn btn-sm btn-primary" title="View Details"><i class="fa fa-eye"></i></button>
        <?php if (\Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_UPDATE)) : ?>
            <button id="tooltip-event-edit" class="btn btn-sm btn-warning" title="Edit event"><i class="fas fa-pencil-square"></i></button>
        <?php endif; ?>
        <?php if ($canViewLogs = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_VIEW_EVENT_LOG)) : ?>
            <button id="tooltip-event-logs" class="btn btn-sm btn-info" title="View Logs"><i class="fas fa-history"></i></button>
        <?php endif; ?>
        <?php if ($canDeleteEvent = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_DELETE)) : ?>
            <button id="tooltip-event-delete" class="btn btn-sm btn-danger" title="Delete Event"><i class="fa fa-trash"></i></button>
        <?php endif; ?>
    </div>
</div>

<?php
$ajaxUrl = Url::to(['shift-schedule/calendar-events-ajax']);
$today = date('Y-m-d', strtotime('+1 day'));
$modalUrl = Url::to(['/shift-schedule/add-event']);
$formCreateSingleEventUrl = Url::to(['/shift-schedule/add-single-event']);
$formUpdateSingleEvent = Url::to(['/shift-schedule/update-single-event']);
$deleteEventUrl = Url::to(['/shift-schedule/delete-event']);
$canCreateOnDoubleClick = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE_ON_DOUBLE_CLICK);
$openModalEventUrl = \yii\helpers\Url::to(['shift-schedule/get-event']);
$viewLogsUrl = \yii\helpers\Url::to(['shift-schedule/ajax-get-logs']);
$multipleDeleteUrl = Url::to(['shift-schedule/ajax-multiple-delete']);
$multipleUpdateUrl = Url::to(['/shift-schedule/ajax-multiple-update']);
$editEventUrl = Url::to(['shift-schedule/ajax-edit-event-form']);
/** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_PERMANENTLY_DELETE, Access to permanently delete event in calendar widget */
$canPermanentlyDeleteEvent = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_PERMANENTLY_DELETE);
/** @abac ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_MULTIPLE_PERMANENTLY_DELETE_EVENTS, Access to delete multiple events permanently */
$canMultiplePermanentlyDeleteEvents = Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_MULTIPLE_PERMANENTLY_DELETE_EVENTS);
$js = <<<JS
var calendarEventsAjaxUrl = '$ajaxUrl';
var today = '$today';
var modalUrl = '$modalUrl';
var events;
var canDeleteEvent = Boolean('$canDeleteEvent');
var canPermanentlyDeleteEvent = Boolean('$canPermanentlyDeleteEvent');
var canMultiplePermanentlyDeleteEvents = Boolean('$canMultiplePermanentlyDeleteEvents');
var canCreateOnDoubleClick = Boolean('$canCreateOnDoubleClick');
var openModalEventUrl = '$openModalEventUrl';
var viewLogsModalUrl = '$viewLogsUrl';

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
var \$status = $('#tooltip-event-status');
var \$title = $('#tooltip-event-title');
var \$view = $('#tooltip-event-view');
var \$viewLogs = $('#tooltip-event-logs');
var \$editBtn = $('#tooltip-event-edit');
var dblClickResource;

var multipleMangeMode = false;
var selectedEventsIds = [];
var checkAllBtn = $('#btn-check-all');


mobiscroll.setOptions({
    theme: 'ios',
    themeVariant: 'light'
});

mobiscroll.momentTimezone.moment = moment;

window.inst = $('#calendar').mobiscroll().eventcalendar({
        view: {
            timeline: { type: 'day', size: 2 },
            refDate: today,
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
//         resources: resourceListJson,
        
        renderResource: function (resource) {
             return '<div class="md-work-week-cont" title="'+resource.title+'">' +
                 '<div class="md-work-week-name" style="display: flex; justify-content: space-between;"><span>' + resource.name + '</span> <span style="margin-right: 10px;">' + resource.icons.join(" ") + '</span></div>' +
                 '<div class="md-work-week-description">' + resource.description + '</div>' +
             '</div>';
        },
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
            if (multipleMangeMode) {
                $('.selected-event').remove();
                selectedEventsIds = [];
                checkAllBtn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Select All');
            }
            let year = event.firstDay.getUTCFullYear(),
                month = event.firstDay.getUTCMonth() + 1,
                day = event.firstDay.getUTCDate();
            
            let endYear = event.lastDay.getUTCFullYear(),
                endMonth = event.lastDay.getUTCMonth() + 1,
                endDay = event.lastDay.getUTCDate();
            
            //alert(event.lastDay.getUTCDay());
            
            let startDate = year + '-' + month + '-' + day;
            let endDate = endYear + '-' + endMonth + '-' + endDay;
            
            $('#startDate').val(startDate);
            $('#endDate').val(endDate);
            
            let btn = $('#filter-calendar-form-btn');
            let btnHtml = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm"></span> Loading').prop("disabled", true);
            getCalendarEvents(getFormFilterData())
              .finally(() => {
                  btn.html(btnHtml).prop("disabled", false);
              });
        },
        onCellDoubleClick: function (args, inst) {
            if (multipleMangeMode) {
                createNotify('Warning', 'You cannot add event in multiple manage mode', 'warning');
                return false;
            }
            dblClickResource = args.resource;
        },
        onEventDragStart: function (args) {
            args.resource = dblClickResource;
        },
        onCellClick: function (args) {
            if (multipleMangeMode) {
                return false;
            }
            dblClickResource = args.resource;
        },
        
        
         onEventCreate: function (args, inst) {
            if (multipleMangeMode) {
                return false;
            }
            if (dblClickResource && args.event.resource !== dblClickResource) {
                args.event.resource = dblClickResource;
            }
            dblClickResource = '';
            
            if (args.event.resource.indexOf('us-') !== 0) {
                inst.removeEvent(args.event);
                return false;
            }
            // store temporary event
            createUpdateEvent(args.event, true);
            /*tempMeal = args.event;
            setTimeout(function () {
                addMealPopup();
            }, 100);*/
        },
        
        onEventClick: function (args, inst) {
            var event = args.event;
            if (!multipleMangeMode) {
                // var resource = events.find(function (e) {return e.resource === event.resource});
                let startDate = new Date(event.start);
                let endDate = new Date(event.end);
                var time = formatDate('YYYY-MM-DD H:mm', startDate) + ' - ' + formatDate('YYYY-MM-DD H:mm', endDate);
                var button = {};
    
                currentEvent = event;
    
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
                \$title.text(event.description);
    
                \$status.text(event.status);
    
                clearTimeout(timer);
                timer = null;
    
                tooltip.setOptions({ anchor: args.domEvent.target });
                tooltip.open();
            } else {
                let scheduleEvent = $(args.domEvent.target).closest('.mbsc-schedule-event');
                let selectedEvent = scheduleEvent.find('.selected-event');
                let index = selectedEventsIds.indexOf(event.id);
                if (index === -1) {
                    scheduleEvent.append(selectedEventTemplate());
                    selectedEventsIds.push(event.id);
                    checkAllBtn.removeClass('btn-default').addClass(['btn-warning', 'checked']).html('<span class="fa fa-check-square-o"></span> Uncheck All (' + selectedEventsIds.length + ')');
                } else {
                    selectedEventsIds.splice(index, 1);
                    selectedEvent.remove();
                    if (selectedEventsIds.length) {
                        checkAllBtn.removeClass('btn-default').addClass(['btn-warning', 'checked']).html('<span class="fa fa-check-square-o"></span> Uncheck All (' + selectedEventsIds.length + ')');
                    } else {
                        checkAllBtn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Select All');
                    }
                }
                console.log(selectedEventsIds);
            }
        },
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
        onEventUpdated: function (args) {
            let event = args.event
            let oldEvent = args.oldEvent;
            mobiscroll.confirm({
                title: 'Are you sure you want to update event?',
                okText: 'Yes',
                cancelText: 'No',
                callback: function (res) {
                    if (res) {
                        let currentUserId;
                        let oldUserId;
                        
                        if (event.resource.indexOf('us-') === 0) {
                            currentUserId = event.resource.substring(3);
                        } else {
                            inst.removeEvent(event);
                            window.inst.addEvent(oldEvent);
                            return false;
                        }
                        if (oldEvent.resource.indexOf('us-') === 0) {
                            oldUserId = oldEvent.resource.substring(3);
                        } else {
                            inst.removeEvent(event);
                            window.inst.addEvent(oldEvent);
                            return false;
                        }
                        
                        let eventStartDate = new Date(event.start);
                        let [year, month, day, hour, minute] = [eventStartDate.getFullYear(), eventStartDate.getMonth()+1, eventStartDate.getDate(), eventStartDate.getHours(), eventStartDate.getMinutes(), eventStartDate];
                        let startDate = year + '-' + month + '-' + day + ' ' + hour + ':' + minute;
                        
                        let eventEndDate = new Date(event.end);
                        let [yearEnd, monthEnd, dayEnd, hourEnd, minuteEnd] = [eventEndDate.getFullYear(), eventEndDate.getMonth()+1, eventEndDate.getDate(), eventEndDate.getHours(), eventEndDate.getMinutes(), eventEndDate];
                        let endDate = yearEnd + '-' + monthEnd + '-' + dayEnd + ' ' + hourEnd + ':' + minuteEnd;
                        
                        let data = {
                            eventId: args.event.id,
                            newUserId: currentUserId,
                            oldUserId: oldUserId,
                            startDate: startDate,
                            endDate: endDate
                        };
                        
                        $.post('$formUpdateSingleEvent', data, function (data) {
                            if (data.error) {
                                createNotify('Error', data.message, 'error');
                            } else {
                                mobiscroll.toast({
                                    message: 'Event updated successfully'
                                });
                            }
                        });
                    } else {
                        inst.removeEvent(event);
                        inst.addEvent(oldEvent);
                    }
                }
            });
        
        },
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
                modal.find('.modal-title').html('Add event for user');
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
            } else {
                // $.post('$formUpdateSingleEvent', {});
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
                        timeline: { type: 'month', timeCellStep: 360, timeLabelStep: 360 },
                        refDate: today
                    }
                })
                break;
                
            case '7day':
                inst.setOptions({
                    view: {
                        timeline: { type: 'day', timeCellStep: 360, timeLabelStep: 360, size: 7 },
                        refDate: today
                    },
                })
                break;
                
            case '30days':
                inst.setOptions({
                    view: {
                        timeline: { type: 'day', timeCellStep: 720, timeLabelStep: 720, size: 30 },
                        refDate: today
                    },
                })
                break;
                
            case 'week':
                inst.setOptions({
                    view: {
                        timeline: { type: 'week', timeCellStep: 720, timeLabelStep: 720 },
                        refDate: today
                    }
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
    
    \$view.on('click', function (e) {
        e.preventDefault()
        openModalEventId(currentEvent.id);
        tooltip.close();
    });
    
    \$editBtn.on('click', function (e) {
        e.preventDefault()
        openModalEdit(currentEvent.id);
        tooltip.close();
    });
    
    \$viewLogs.on('click', function (e) {
        e.preventDefault()
        openLogsModal(currentEvent.id);
        tooltip.close();
    });

    if (canDeleteEvent) {
        \$deleteButton.on('click', function (ev) {
            if(canPermanentlyDeleteEvent) {
                setTimeout(function (args) {
                    let html = '' +
                    '<label>'+
                        '<input type="checkbox" id="delete_permanently" mbsc-checkbox data-label="Delete Permanently" data-color="danger" />'+
                    '</label>';
                    var messageContent = $('.mbsc-alert-message');
                    messageContent.html(html);
                    mobiscroll.enhance(messageContent[0]);
                }, 100);
            }
            
            mobiscroll.confirm({
                title: 'Are you sure you want to delete event?',
                message: '',
                okText: 'Yes',
                cancelText: 'No',
                callback: function (res) {
                    if (res) {
                        let deletePermanently = $('#delete_permanently').is(':checked') ? 1 : 2;
                        $.ajax({
                            url: '$deleteEventUrl',
                            data: {'shiftId': currentEvent.id, 'deletePermanently' : deletePermanently},
                            type: 'post',
                            cache: false,
                            dataType: 'json',
                            success: function (data) {
                                if (data.error) {
                                    createNotify('Error', data.message, 'error');
                                } else {
                                    inst.removeEvent(currentEvent.id);
                                    if(data.timelineData){
                                        addTimelineEvent(JSON.parse(data.timelineData))
                                    }
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

    function getCalendarEvents(queryString) {
        return new Promise(function (resolve, reject) {
            $.getJSON(calendarEventsAjaxUrl + '?' + queryString, function (data) {
                    if (data.error) {
                        createNotify('Error', data.message, 'error');
                        reject();
                    } else {
                        inst.setOptions({
                            resources: data.resources
                        });
                        setTimelineEvents(data.data);
                
                        mobiscroll.toast({
                            message: 'New events loaded'
                        });
                        resolve();
                    }
                }, 'jsonp');
            
        });
    }
    
    function getFormFilterData()
    {
        let form = document.getElementById('filter-calendar-form');
        let formData = new FormData(form);
        formData.delete('_csrf-frontend');
        return new URLSearchParams(formData).toString();
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
    
    window.addTimelineEvents = function (data) {
        data.forEach(function (event, i) {
            window.inst.addEvent(event);
            events.push(event);
        });
    } 
    // $.getJSON(calendarEventsAjaxUrl, function (events) {
    //     inst.setEvents(events);
    // }, 'jsonp');
    
    $('#btn-shift-event-add').on('click', function (e) {
        e.preventDefault(); 
        if (multipleMangeMode) {
            createNotify('Warning', 'You cannot perform this action in multiple manage mode', 'warning');
            return false;
        }
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
    
    function openModalEdit(id)
    {
        let modal = $('#modal-md');
        modal.find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        modal.find('.modal-title').html('Edit event');
        modal.find('.modal-body').load('{$editEventUrl}?eventId=' + id, {}, function( response, status, xhr ) {
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
    }
    
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
    
    function openLogsModal(id)
    {
        let modal = $('#modal-lg');
        let eventUrl = viewLogsModalUrl + '?id=' + id;
        //modal.find('.modal-title').html('Offer [' + gid + '] status history');
        $('#modal-md-label').html('Schedule Event Logs: ' + id);
        modal.find('.modal-body').html('');
        modal.find('.modal-body').load(eventUrl, function( response, status, xhr ) {
            if (status === 'error') {
                createNotify('Error', response, 'error');
            } else {
                modal.modal('show');
            }
        });
    } 

    $('#filter-calendar-form-btn').on('click', function (e) {
        e.preventDefault();
        let btn = $(this);
        let btnHtml = btn.html();
        btn.html('<span class="spinner-border spinner-border-sm"></span> Loading').prop("disabled", true);        
        let queryString = getFormFilterData();
        $('#calendar-wrapper').append(loaderTemplate());
        getCalendarEvents(queryString)
        .then(() => {
            window.history.replaceState(null, null, '?' + queryString + '&appliedFilter=1');  
        })
        .finally(() => {
            btn.html(btnHtml).prop("disabled", false);
            $('#calendar-wrapper .calendar-filter-overlay').remove();
        });
    });
    
    function loaderTemplate(modal) {
        return `<div class="text-center calendar-filter-overlay">
            <div class="spinner-border m-5" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>`;
    }
    
    function selectedEventTemplate()
    {
        // return `<div class="selected-event">
        //     <span>
        //         <i class="fa fa-flag"></i>
        //     </span>
        // </div>`;
        return `<div class="selected-event">
               <span class="circle"></span>
        </div>`;
    }
    
    var multipleManageBtn = $('#multiple-manage-mode-btn');
    var checkAllBtnWrapper = $('#check_uncheck_btns');
    var exitModeBtn = $('#btn-multiple-exit-mode');
    
    multipleManageBtn.on('click', function (e) {
        e.preventDefault();
        $(this).hide();
        checkAllBtnWrapper.show();
        exitModeBtn.show();
        multipleMangeMode = true;
        inst.setOptions({
            clickToCreate: false,
            dragToCreate: false,
            dragToMove: false,
            dragToResize: false,
        });
        $('#calendar').addClass('multiple-manage-mode');
    });
    exitModeBtn.on('click', function (e) {
        e.preventDefault();
        checkAllBtnWrapper.hide();
        exitModeBtn.hide();
        multipleManageBtn.show();
        multipleMangeMode = false;
        inst.setOptions({
            clickToCreate: canCreateOnDoubleClick,
            dragToCreate: false,
            dragToMove: true,
            dragToResize: true,
        });
        $('.selected-event').remove();
        selectedEventsIds = [];
        checkAllBtn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Select All');
        $('#calendar').removeClass('multiple-manage-mode');
    });
    checkAllBtn.on('click', function (e) {
        e.preventDefault();
        if (!inst._events.length) {
            createNotify('Warning', 'There are no events. Update the filter or add another event.', 'warning');
            return false;
        }
        let btn = $(this);
        
        if (selectedEventsIds.length) {
            $('.selected-event').remove();
            selectedEventsIds = [];
            btn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Select All');
        } else {
            inst._events.forEach(function (e, i) {
                selectedEventsIds.push(e.id);
            });
            $('.mbsc-schedule-event').append(selectedEventTemplate());
            btn.removeClass('btn-default').addClass(['btn-warning', 'checked']).html('<span class="fa fa-check-square-o"></span> Uncheck All (' + selectedEventsIds.length + ')');
        }
    });
    
    $('.btn-multiple-delete-events').on('click', function (e) {
        e.preventDefault();
        if (!selectedEventsIds.length) {
            createNotify('Warning', 'You have not selected any events', 'warning');
            return false;
        }
        if(canMultiplePermanentlyDeleteEvents) {
            setTimeout(function (args) {
                let html = '' +
                '<label>'+
                    '<input type="checkbox" id="delete_permanently" mbsc-checkbox data-label="Delete Permanently" data-color="danger" />'+
                '</label>';
                var messageContent = $('.mbsc-alert-message');
                messageContent.html(html);
                mobiscroll.enhance(messageContent[0]);
            }, 100);
        }
            
        mobiscroll.confirm({
            title: 'Are you sure you want to delete event(s)?',
            message: '',
            okText: 'Yes',
            cancelText: 'No',
            callback: function (res) {
                if (res) {                    
                    let deletePermanently = $('#delete_permanently').is(':checked') ? 1 : 2;
                    $('#calendar-wrapper').append(loaderTemplate());
                    $.ajax({
                        url: '$multipleDeleteUrl',
                        type: 'post',
                        dataType: 'json',
                        cache: false,
                        data: {selectedEvents: selectedEventsIds, 'deletePermanently' : deletePermanently},
                        success: function (data) {
                            if (data.error) {
                                createNotify('Error', data.message, 'error');
                            } else {
                                selectedEventsIds.forEach(function (id, i) {
                                    inst.removeEvent(id);
                                });
                                
                                let messasgePart = 'removed';
                                let timeLineData = JSON.parse(data.timelineData);
                                if(timeLineData.length){
                                    addTimelineEvent(timeLineData);
                                    messasgePart = 'deleted';
                                }
                                $('.selected-event').remove();
                                createNotify('Success', 'Event(s) successfully ' + messasgePart, 'success');
                                selectedEventsIds = [];
                                checkAllBtn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Select All');
                            }
                        },
                        error: function (xhr) {
                            createNotify('Error', xhr.responseText, 'error');
                        },
                        complete: function () {
                            $('#calendar-wrapper .calendar-filter-overlay').remove();
                        }
                    })
                }
            }
        });
    });
    
    $('.btn-multiple-update-events').on('click', function (e) {
        e.preventDefault();
        if (!selectedEventsIds.length) {
            createNotify('Warning', 'You have not selected any events', 'warning');
            return false;
        }
        
        let modal = $('#modal-md');
        modal.find('.modal-title').html('Multiple update selected Shift(s)')
        modal.find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        modal.find('.modal-body').load('$multipleUpdateUrl', {}, function( response, status, xhr ) {
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
    
    $(document).on('submit','form#multiple-update-events-form', function(){
        $('#eventIds').val(JSON.stringify(selectedEventsIds));
        let btnObj = $('#submit-multiple-update-events');
        btnObj.html('<i class="fa fa-spin fa-spinner"></i>');
        btnObj.addClass('disabled').prop('disabled', true);
    });
JS;

$this->registerJs($js);
