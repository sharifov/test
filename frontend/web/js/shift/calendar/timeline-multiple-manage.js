;(function (window, $) {
    'use strict';
    var App = window.App || {};

    var checkAllBtn;
    var multipleManageBtn;
    var checkAllBtnWrapper;
    var exitModeBtn;
    var multipleAddEventsBtn;
    var multipleUpdateEventsBtn;
    var multipleDeleteEventsBtn;

    function MultipleManageModule(
        canMultipleAdd,
        canMultipleUpdate,
        canSoftDelete,
        canDelete,
        multipleAddUrl,
        multipleUpdateUrl,
        multipleDeleteUrl
    ) {
        this.enabled = false;
        this.selectedEventsIds = [];
        this.canMultipleAdd = canMultipleAdd;
        this.canMultipleUpdate = canMultipleUpdate;
        this.canSoftDelete = canSoftDelete;
        this.canDelete = canDelete;
        this.multipleAddUrl = multipleAddUrl;
        this.multipleUpdateUrl = multipleUpdateUrl;
        this.multipleDeleteUrl = multipleDeleteUrl;
        this.timeline = null;
    }

    MultipleManageModule.prototype.init = function (selectorId, timeline) {
        this.timeline = timeline;

        $('#' + selectorId).html(moduleTemplate.call(this));
        checkAllBtn = $('#btn-check-all');
        multipleManageBtn = $('#multiple-manage-mode-btn');
        checkAllBtnWrapper = $('#check_uncheck_btns');
        exitModeBtn = $('#btn-multiple-exit-mode');
        multipleAddEventsBtn = $('#btn-shift-event-add');
        multipleUpdateEventsBtn = $('#btn-multiple-update-events');
        multipleDeleteEventsBtn = $('#btn-multiple-delete-events');

        checkAllBtn.on('click', checkAllBtnClick.bind(this));
        multipleManageBtn.on('click', multipleManageBtnClick.bind(this));
        exitModeBtn.on('click', exitModeBtnClick.bind(this));
        multipleUpdateEventsBtn.on('click', multipleUpdateEventClick.bind(this));
        multipleDeleteEventsBtn.on('click', multipleDeleteEventClick.bind(this));
        multipleAddEventsBtn.on('click', multipleAddEventClick.bind(this));
    };

    MultipleManageModule.prototype.enable = function () {
        this.enabled = true;
    };

    MultipleManageModule.prototype.disable = function () {
        this.enabled = false;
    };

    MultipleManageModule.prototype.isEnabled = function () {
        return this.enabled === true;
    };

    MultipleManageModule.prototype.resetSelectedEvents = function () {
        $('.selected-event').remove();
        this.selectedEventsIds = [];
        checkAllBtn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Select All');
    };

    MultipleManageModule.prototype.toggleEventSelection = function (target, event) {
        let scheduleEvent = $(target).closest('.mbsc-schedule-event');
        let selectedEvent = scheduleEvent.find('.selected-event');
        let index = this.selectedEventsIds.indexOf(event.id);
        if (index === -1) {
            scheduleEvent.append(selectedEventTemplate());
            this.selectedEventsIds.push(event.id);
            checkAllBtn.removeClass('btn-default').addClass(['btn-warning', 'checked']).html('<span class="fa fa-check-square-o"></span> Uncheck All (' + this.selectedEventsIds.length + ')');
        } else {
            this.selectedEventsIds.splice(index, 1);
            selectedEvent.remove();
            if (this.selectedEventsIds.length) {
                checkAllBtn.removeClass('btn-default').addClass(['btn-warning', 'checked']).html('<span class="fa fa-check-square-o"></span> Uncheck All (' + this.selectedEventsIds.length + ')');
            } else {
                checkAllBtn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Select All');
            }
        }
    };

    function moduleTemplate()
    {
        return `
        ${multipleAddBtnTemplate(this.canMultipleAdd)}
        <a id="multiple-manage-mode-btn" class="btn btn-warning btn-sm"><i class="fa fa-th-large"></i> Multiple Manage Mode</a>
        <a id="btn-multiple-exit-mode" class="btn btn-danger btn-sm" style="display: none;"><i class="fas fa-times-circle"></i> Exit Mode</a>
        
        <div class="btn-group" id="check_uncheck_btns" style="display: none; margin-bottom: 4px; height: 28px; margin-left: 7px;">
            <button id="btn-check-all" class="btn btn-sm btn-default"><span class="fa fa-square-o"></span> Select All</button>

            <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu">
                  ${multipleUpdateBtnTemplate(this.canMultipleUpdate)}
                  ${multipleDeleteBtnTemplate(this.canMultipleUpdate)}
            </div>
        </div>
        `;
    }

    function multipleUpdateBtnTemplate(canMultipleUpdate)
    {
        return canMultipleUpdate ? `<a class="dropdown-item" id="btn-multiple-update-events"><i class="fa fa-edit text-warning"></i> Update Events</a>` : '';
    }

    function multipleDeleteBtnTemplate(canSoftDelete)
    {
        return canSoftDelete ? `<a class="dropdown-item" id="btn-multiple-delete-events"><i class="fa fa-trash text-danger"></i> Delete Events</a>` : '';
    }

    function multipleAddBtnTemplate(canMultipleAdd)
    {
        return canMultipleAdd ? `<a class="btn btn-success btn-sm" id="btn-shift-event-add"><i class="fa fa-plus-circle"></i> Add Multiple Events</a>` : '';
    }

    function selectedEventTemplate()
    {
        return `
        <div class="selected-event">
               <span class="circle"></span>
        </div>`.trim();
    }

    function checkAllBtnClick(e)
    {
        e.preventDefault();
        let events = this.timeline.getEvents();
        if (!events.length) {
            createNotify('Warning', 'There are no events. Update the filter or add another event.', 'warning');
            return false;
        }
        let btn = $(e.target);
        if (this.selectedEventsIds.length) {
            $('.selected-event').remove();
            this.selectedEventsIds = [];
            btn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Select All');
        } else {
            events.forEach(function (e, i) {
                this.selectedEventsIds.push(e.id);
            }.bind(this));
            $('.mbsc-schedule-event').append(selectedEventTemplate());
            btn.removeClass('btn-default').addClass(['btn-warning', 'checked']).html('<span class="fa fa-check-square-o"></span> Uncheck All (' + this.selectedEventsIds.length + ')');
        }
    }

    function multipleManageBtnClick(e) {
        e.preventDefault();
        $(e.target).hide();
        checkAllBtnWrapper.show();
        exitModeBtn.show();
        this.enable();
        this.timeline.timeline.setOptions({
            clickToCreate: false,
            dragToCreate: false,
            dragToMove: false,
            dragToResize: false,
        });
        $('#calendar').addClass('multiple-manage-mode');
    }

    function exitModeBtnClick (e) {
        e.preventDefault();
        checkAllBtnWrapper.hide();
        exitModeBtn.hide();
        multipleManageBtn.show();
        this.disable();
        this.timeline.refreshManageOptions();
        $('.selected-event').remove();
        this.selectedEventsIds = [];
        checkAllBtn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Select All');
        $('#calendar').removeClass('multiple-manage-mode');
    }

    function multipleUpdateEventClick(e)
    {
        e.preventDefault();
        if (!this.selectedEventsIds.length) {
            createNotify('Warning', 'You have not selected any events', 'warning');
            return false;
        }

        let modal = $('#modal-md');
        modal.find('.modal-title').html('Multiple update selected Shift(s)');
        modal.find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        modal.find('.modal-body').load(this.multipleUpdateUrl, {eventIds: JSON.stringify(this.selectedEventsIds), showForm: 1}, function( response, status, xhr ) {
            if (status == 'error') {
                createNotifyByObject({
                    'title': 'Error',
                    'type': 'error',
                    'text': xhr.statusText
                });
            } else {
                modal.modal({
                    backdrop: 'static',
                    show: true
                });
            }
        });
    }

    function multipleAddEventClick(e)
    {
        e.preventDefault();
        if (this.isEnabled()) {
            createNotify('Warning', 'You cannot perform this action in multiple manage mode', 'warning');
            return false;
        }
        let modal = $('#modal-md');
        modal.find('.modal-title').html('Add Multiple Events');
        modal.find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        modal.find('.modal-body').load(this.multipleAddUrl, {}, function( response, status, xhr ) {
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

    function multipleDeleteEventClick(e)
    {
        e.preventDefault();
        if (!this.selectedEventsIds.length) {
            createNotify('Warning', 'You have not selected any events', 'warning');
            return false;
        }

        if(this.canDelete) {
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
                    this.timeline.enableLoader();
                    $.ajax({
                        url: this.multipleDeleteUrl,
                        type: 'post',
                        dataType: 'json',
                        cache: false,
                        data: {selectedEvents: this.selectedEventsIds, 'deletePermanently' : deletePermanently},
                        success: function (data) {
                            if (data.error) {
                                createNotify('Error', data.message, 'error');
                            } else {
                                this.selectedEventsIds.forEach(function (id, i) {
                                    this.timeline.removeEvent(id);
                                }.bind(this));

                                if(data.timelineData){
                                    this.timeline.addEvents(JSON.parse(data.timelineData));
                                }
                                $('.selected-event').remove();
                                createNotify('Success', 'Event(s) successfully deleted', 'success');
                                this.selectedEventsIds = [];
                                checkAllBtn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Select All');
                            }
                        }.bind(this),
                        error: function (xhr) {
                            createNotify('Error', xhr.responseText, 'error');
                        },
                        complete: function () {
                            this.timeline.disableLoader();
                        }.bind(this)
                    });
                }
            }.bind(this)
        });
    }

    App.MultipleManageModule = MultipleManageModule;
    window.App = App;
})(window, $);