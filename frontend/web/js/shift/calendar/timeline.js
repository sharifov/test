;(function (window, $, mobiscroll, moment) {
    'use strict';
    var App = window.App || {};

    var currentEvent;
    var formatDate = mobiscroll.util.datetime.formatDate;
    var elementSelectorWrapper;

    var canCreate = false;
    var canUpdate = false;

    function ShiftTimeline(
        elementSelectorId,
        elementSelectorWrapperId,
        formFilter,
        multipleManageModule,
        calendarEventsAjaxUrl,
        createSingleEventFormUrl,
        updateEventUrl,
        tooltip,
        today
    ) {
        this.timelineElementSelectorId = elementSelectorId;
        this.elementSelectorWrapperId = elementSelectorWrapperId;
        this.timeline = null;
        this.formFilter = formFilter;
        this.multipleManageModule = multipleManageModule;
        this.calendarEventsAjaxUrl = calendarEventsAjaxUrl;
        this.createSingleEventFormUrl = createSingleEventFormUrl;
        this.updateEventUrl = updateEventUrl;
        this.invalidGroupResources = [];
        this.tooltip = tooltip;
        this.today = today;
    }

    ShiftTimeline.prototype.init = function (dto) {
        mobiscroll.momentTimezone.moment = moment;

        canCreate = dto.canCreate;
        canUpdate = dto.canUpdate;

        elementSelectorWrapper = $('#' + this.elementSelectorWrapperId);
        this.timeline = $('#' + this.timelineElementSelectorId).mobiscroll().eventcalendar({
            view: {
                timeline: { type: 'day', size: 2 },
                refDate: this.today,
            },
            theme: 'ios',
            themeVariant: 'light',
            timeFormat: 'HH:mm',
            dataTimezone: 'utc',
            displayTimezone: dto.userTimeZone,
            timezonePlugin: mobiscroll.momentTimezone,
            clickToCreate: canCreate,
            dragToCreate: canCreate,
            dragToMove: canUpdate,
            dragToResize: canUpdate,
            renderResource: resourceTemplate.bind(this),
            renderHeader: headerTemplate.bind(this),
            onPageLoading: onPageLoading.bind(this),
            onCellDoubleClick: function (args, inst) {
                if (this.multipleManageModule.isEnabled()) {
                    createNotify('Warning', 'You cannot add event in multiple manage mode', 'warning');
                    return false;
                }
            }.bind(this),
            onEventDragEnd: function (args, inst) {
                this.timeline.setOptions({
                    invalid: this.invalidGroupResources,
                    colors: []
                });
            }.bind(this),
            onCellClick: function (args) {
                if (this.multipleManageModule.isEnabled()) {
                    return false;
                }
            }.bind(this),
            onEventCreated: function (args, inst) {
                if (this.multipleManageModule.isEnabled()) {
                    return false;
                }
                createEvent.call(this, args.event);
            }.bind(this),
            onEventClick: timelineEventClick.bind(this),
            onEventUpdated: timelineEventUpdate.bind(this)

        }).mobiscroll('getInst');

        $('.md-timeline-view-change').change(function (ev) {
            switch (ev.target.value) {
                case 'day':
                    this.timeline.setOptions({
                        view: {
                            timeline: { type: 'day', size: 2 },
                            refDate: this.today
                        }
                    });
                    if (this.multipleManageModule.isEnabled()) {
                        this.multipleManageModule.resetSelectedEvents();
                    }
                    break;
                case 'month':
                    this.timeline.setOptions({
                        view: {
                            timeline: { type: 'month', timeCellStep: 360, timeLabelStep: 360 },
                            refDate: this.today
                        }
                    });
                    if (this.multipleManageModule.isEnabled()) {
                        this.multipleManageModule.resetSelectedEvents();
                    }
                    break;
                case '7day':
                    this.timeline.setOptions({
                        view: {
                            timeline: { type: 'day', timeCellStep: 360, timeLabelStep: 360, size: 7 },
                            refDate: this.today
                        },
                    });
                    if (this.multipleManageModule.isEnabled()) {
                        this.multipleManageModule.resetSelectedEvents();
                    }
                    break;
                case '30days':
                    this.timeline.setOptions({
                        view: {
                            timeline: { type: 'day', timeCellStep: 720, timeLabelStep: 720, size: 30 },
                            refDate: this.today
                        },
                    });
                    if (this.multipleManageModule.isEnabled()) {
                        this.multipleManageModule.resetSelectedEvents();
                    }
                    break;
                case 'week':
                    this.timeline.setOptions({
                        view: {
                            timeline: { type: 'week', timeCellStep: 720, timeLabelStep: 720 },
                            refDate: this.today
                        }
                    });
                    if (this.multipleManageModule.isEnabled()) {
                        this.multipleManageModule.resetSelectedEvents();
                    }
                    break;
                case 'month-day':
                    this.timeline.setOptions({
                        view: {
                            timeline: { type: 'month', timeCellStep: 1440, timeLabelStep: 1440 }
                        }
                    });
                    if (this.multipleManageModule.isEnabled()) {
                        this.multipleManageModule.resetSelectedEvents();
                    }
                    break;
            }
        }.bind(this));

        this.tooltip.init(dto.tooltipElementSelectorId, this);
        this.multipleManageModule.init(dto.multipleModuleElementSelectorId, this);
        this.formFilter.init(this);
    };

    ShiftTimeline.prototype.getCalendarEvents = function (queryString) {
        return new Promise(function (resolve, reject) {
            $.getJSON(this.calendarEventsAjaxUrl + '?' + queryString, function (data) {
                if (data.error) {
                    createNotify('Error', data.message, 'error');
                    reject();
                } else {
                    this.invalidGroupResources = [{
                        recurring: {
                            repeat: 'daily'
                        },
                        resource: data.firstLevelResources
                    }];
                    this.timeline.setOptions({
                        resources: data.resources,
                        invalid: this.invalidGroupResources
                    });
                    this.setEvents(data.data);

                    mobiscroll.toast({
                        message: 'New events loaded'
                    });
                    resolve();
                }
            }.bind(this), 'jsonp');

        }.bind(this));
    };

    ShiftTimeline.prototype.setEvents = function (data) {
        this.timeline.setEvents(data);
    };

    ShiftTimeline.prototype.addEvent = function (data) {
        this.timeline.addEvent(data);
    };

    ShiftTimeline.prototype.addEvents = function (data) {
        data.forEach(function (e) {
            this.timeline.addEvent(e);
        }.bind(this));
    };

    ShiftTimeline.prototype.removeEvent = function (data) {
        this.timeline.removeEvent(data);
    };

    ShiftTimeline.prototype.enableLoader = function () {
        elementSelectorWrapper.append(loaderTemplate());
    };

    ShiftTimeline.prototype.disableLoader = function () {
        $('.calendar-filter-overlay').remove();
    };

    ShiftTimeline.prototype.getEvents = function () {
        return this.timeline._events;
    };

    ShiftTimeline.prototype.refreshManageOptions = function () {
        this.timeline.setOptions({
            clickToCreate: canCreate,
            dragToCreate: canCreate,
            dragToMove: canUpdate,
            dragToResize: canUpdate,
        });
    };

    function loaderTemplate() {
        return `<div class="text-center calendar-filter-overlay">
            <div class="spinner-border m-5" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>`;
    }

    function resourceTemplate(resource) {
        return `
        <div class="md-work-week-cont" title="'+resource.title+'">
            <div class="md-work-week-name" style="display: flex; justify-content: space-between;">
                <span>${resource.name}</span> <span style="margin-right: 10px;">${resource.icons.join(" ")}</span>
            </div>
            <div class="md-work-week-description">${resource.description}</div>
        </div>`.trim();
    }

    function headerTemplate() {
        return `
            <div mbsc-calendar-nav class="md-work-week-nav"></div>
            <div class="md-work-week-picker">
                <label>Day (hours)<input mbsc-segmented type="radio" name="switching-timeline-view" value="day" class="md-timeline-view-change" checked></label>
                <label>7 Days<input mbsc-segmented type="radio" name="switching-timeline-view" value="7day" class="md-timeline-view-change"></label>
                <label>30 Days<input mbsc-segmented type="radio" name="switching-timeline-view" value="30days" class="md-timeline-view-change"></label>
                <label>Week<input mbsc-segmented type="radio" name="switching-timeline-view" value="week" class="md-timeline-view-change"></label>
                <label>Month<input mbsc-segmented type="radio" name="switching-timeline-view" value="month" class="md-timeline-view-change"></label>
                <label>Month (day)<input mbsc-segmented type="radio" name="switching-timeline-view" value="month-day" class="md-timeline-view-change"></label>
            </div>
            <div mbsc-calendar-prev class="md-work-week-prev"></div>
            <div mbsc-calendar-today class="md-work-week-today"></div>
            <div mbsc-calendar-next class="md-work-week-next"></div>`.trim();
    }

    function onPageLoading(event) {
        if (this.multipleManageModule.isEnabled()) {
            this.multipleManageModule.resetSelectedEvents();
        }

        let year = event.firstDay.getUTCFullYear(),
            month = event.firstDay.getUTCMonth() + 1,
            day = event.firstDay.getUTCDate();

        let endYear = event.lastDay.getUTCFullYear(),
            endMonth = event.lastDay.getUTCMonth() + 1,
            endDay = event.lastDay.getUTCDate();

        let startDate = year + '-' + month + '-' + day;
        let endDate = endYear + '-' + endMonth + '-' + endDay;

        $('#startDate').val(startDate);
        $('#endDate').val(endDate);

        this.formFilter.disableSubmitBtn();
        this.getCalendarEvents(this.formFilter.getQueryString())
            .finally(function () {
                this.formFilter.enableSubmitBtn();
            }.bind(this));
    }

    function createEvent(event) {
        let userId = event.resource;
        let eventStartDate = new Date(event.start);
        let startDate = getDateTimeFormatted(eventStartDate);
        let modal = $('#modal-md');
        modal.find('.modal-title').html('Add event for user');
        modal.on('hide.bs.modal', function (e) {
            this.removeEvent(event);
        }.bind(this));
        modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        $.get(this.createSingleEventFormUrl + '?userId=' + userId + '&startDate=' + startDate, function(data) {
            modal.find('.modal-body').html(data);
        }).fail(function (xhr) {
            setTimeout(function () {
                modal.modal('hide');
                createNotify('Error', xhr.statusText, 'error');
            }, 800);
        });
    }

    function timelineEventClick(args)
    {
        var event = args.event;
        if (!this.multipleManageModule.isEnabled()) {
            let startDate = new Date(event.start);
            let endDate = new Date(event.end);
            var time = formatDate('YYYY-MM-DD H:mm', startDate) + ' - ' + formatDate('YYYY-MM-DD H:mm', endDate);

            currentEvent = event;

            this.tooltip.setEvent(currentEvent);
            this.tooltip.customize(event.borderColor, event.color, event.title, time, event.description, event.status);
            this.tooltip.setOptions({ anchor: args.domEvent.target });
            this.tooltip.open();
        } else {
            this.multipleManageModule.toggleEventSelection(args.domEvent.target, event);
        }
    }

    function timelineEventUpdate (args) {
        let event = args.event;
        let oldEvent = args.oldEvent;
        mobiscroll.confirm({
            title: 'Are you sure you want to update event?',
            okText: 'Yes',
            cancelText: 'No',
            callback: function (res) {
                if (res) {
                    let currentUserId;
                    let oldUserId;

                    currentUserId = event.resource;
                    oldUserId = oldEvent.resource;

                    let eventStartDate = new Date(event.start);
                    let startDate = getDateTimeFormatted(eventStartDate);

                    let eventEndDate = new Date(event.end);
                    let endDate = getDateTimeFormatted(eventEndDate);

                    let data = {
                        eventId: args.event.id,
                        newUserId: currentUserId,
                        oldUserId: oldUserId,
                        dateTimeStart: startDate,
                        dateTimeEnd: endDate
                    };

                    $.post(this.updateEventUrl, data, function (data) {
                        if (data.error) {
                            createNotify('Error', data.message, 'error');
                        } else {
                            mobiscroll.toast({
                                message: 'Event updated successfully'
                            });
                        }
                    });
                } else {
                    this.removeEvent(event);
                    this.timeline.addEvent(oldEvent);
                }
            }.bind(this)
        });
    }

    function getDateTimeFormatted(dateTime)
    {
        let [year, month, day] = [dateTime.getFullYear(), dateTime.getMonth()+1, dateTime.getDate()];
        let hour = dateTime.getHours();
        hour = (hour < 10 ? '0' : '') + hour;
        let minute = dateTime.getMinutes();
        minute = (minute < 10 ? '0' : '') + minute;
        return year + '-' + month + '-' + day + ' ' + hour + ':' + minute;
    }

    App.ShiftTimeline = ShiftTimeline;
    window.App = App;
})(window, $, mobiscroll, moment);