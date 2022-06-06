;(function (window, $, mobiscroll) {
    'use strict';
    var App = window.App || {};

    var timer;
    var currentEvent;

    function TimelineTooltip(
        canUpdateEvent,
        canViewEventLog,
        canDeleteEvent,
        canPermanentlyDeleteEvent,
        openModalEventDetailsUrl,
        openModalEventLogsUrl,
        openModalEventEditUrl,
        deleteEventUrl
    ) {
        this.canUpdateEvent = canUpdateEvent;
        this.canViewEventLog = canViewEventLog;
        this.canDeleteEvent = canDeleteEvent;
        this.canPermanentlyDeleteEvent = canPermanentlyDeleteEvent;
        this.openModalEventDetailsUrl = openModalEventDetailsUrl;
        this.openModalEventLogsUrl = openModalEventLogsUrl;
        this.openModalEventEditUrl = openModalEventEditUrl;
        this.deleteEventUrl = deleteEventUrl;
    }

    TimelineTooltip.prototype.init = function (elementSelectorId, timeline) {
        this.timeline = timeline;

        $('#' + elementSelectorId).html(tooltipTemplate.call(this));

        this.tooltipElement = $('#custom-event-tooltip-popup');

        this.tooltip = this.tooltipElement.mobiscroll().popup({
            display: 'anchored',
            touchUi: false,
            showOverlay: false,
            contentPadding: false,
            closeOnOverlayClick: false,
            width: 350
        }).mobiscroll('getInst');

        this.tooltipElement.mouseenter(function (ev) {
            if (timer) {
                clearTimeout(timer);
                timer = null;
            }
        }.bind(this));

        this.tooltipElement.mouseleave(function (ev) {
            timer = setTimeout(function () {
                this.tooltip.close();
            }.bind(this), 200);
        }.bind(this));

        this.deleteButton = $('#tooltip-event-delete');
        this.header = $('#tooltip-event-header');
        this.data = $('#tooltip-event-name-age');
        this.time = $('#tooltip-event-time');
        this.status = $('#tooltip-event-status');
        this.title = $('#tooltip-event-title');
        this.view = $('#tooltip-event-view');
        this.viewLogs = $('#tooltip-event-logs');
        this.editBtn = $('#tooltip-event-edit');

        this.view.on('click', function (e) {
            e.preventDefault();
            openModalEventDetails.call(this, currentEvent.id);
            this.tooltip.close();
        }.bind(this));

        this.editBtn.on('click', function (e) {
            e.preventDefault();
            openModalEventEdit.call(this, currentEvent.id);
            this.tooltip.close();
        }.bind(this));

        this.viewLogs.on('click', function (e) {
            e.preventDefault();
            openModalEventLogs.call(this, currentEvent.id);
            this.tooltip.close();
        }.bind(this));

        this.deleteButton.on('click', function (e) {
            e.preventDefault();
            openModalEventDelete.call(this);
            this.tooltip.close();
        }.bind(this));
    };

    TimelineTooltip.prototype.customize = function (borderColor, color, title, time, description, status) {
        this.header.css('background-color', borderColor || color);
        this.data.text(title);
        this.time.text(time);
        this.title.text(description);
        this.status.text(status);
    };

    TimelineTooltip.prototype.setEvent = function (event) {
        currentEvent = event;
    };

    TimelineTooltip.prototype.setOptions = function (options) {
        this.tooltip.setOptions(options);
    };

    TimelineTooltip.prototype.open = function (eventId) {
        clearTimeout(timer);
        timer = null;
        this.tooltip.open();
    };

    function tooltipTemplate()
    {
        return `
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
                ${updateEventBtnTemplate.call(this)}
                ${viewEventLogBtnTemplate.call(this)}
                ${deleteEventBtnTemplate.call(this)}
            </div>
        </div>
        `.trim();
    }

    function updateEventBtnTemplate()
    {
        return this.canUpdateEvent ? `<button id="tooltip-event-edit" class="btn btn-sm btn-warning" title="Edit event"><i class="fas fa-pencil-square"></i></button>` : '';
    }

    function viewEventLogBtnTemplate()
    {
        return this.canViewEventLog ? `<button id="tooltip-event-logs" class="btn btn-sm btn-info" title="View Logs"><i class="fas fa-history"></i></button>` : '';
    }

    function deleteEventBtnTemplate()
    {
        return this.canDeleteEvent ? `<button id="tooltip-event-delete" class="btn btn-sm btn-danger" title="Delete Event"><i class="fa fa-trash"></i></button>` : '';
    }

    function deletePermanentlyCheckboxTemplate()
    {
        return `
        <label>
            <input type="checkbox" id="delete_permanently" mbsc-checkbox data-label="Delete Permanently" data-color="danger" />
        </label>`.trim();
    }

    function openModalEventDetails(id)
    {
        let modal = $('#modal-md');
        let eventUrl = this.openModalEventDetailsUrl + '?id=' + id;
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

    function openModalEventLogs(id)
    {
        let modal = $('#modal-lg');
        let eventUrl = this.openModalEventLogsUrl + '?id=' + id;
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

    function openModalEventEdit(id)
    {
        let modal = $('#modal-md');
        modal.find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        modal.find('.modal-title').html('Edit event');
        modal.find('.modal-body').load(this.openModalEventEditUrl + '?eventId=' + id, {}, function( response, status, xhr ) {
            if (status === 'error') {
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

    function openModalEventDelete()
    {
        if(this.canPermanentlyDeleteEvent) {
            setTimeout(function (args) {
                var messageContent = $('.mbsc-alert-message');
                messageContent.html(deletePermanentlyCheckboxTemplate());
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
                        url: this.deleteEventUrl,
                        data: {'shiftId': currentEvent.id, 'deletePermanently' : deletePermanently},
                        type: 'post',
                        cache: false,
                        dataType: 'json',
                        success: function (data) {
                            if (data.error) {
                                createNotify('Error', data.message, 'error');
                            } else {
                                this.timeline.removeEvent(currentEvent.id);
                                if(data.timelineData) {
                                    this.timeline.addEvent(JSON.parse(data.timelineData));
                                }
                                this.tooltip.close();
                                createNotify('Success', data.message, 'success');
                            }
                        }.bind(this),
                        error: function (xhr) {
                            createNotify('Error', xhr.responseText, 'error');
                        }
                    });
                }
            }.bind(this)
        });
    }

    App.TimelineTooltip = TimelineTooltip;
    window.App = App;
})(window, $, mobiscroll);