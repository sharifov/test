var PhoneWidgetCall = function () {

    this.connection = '';

    let statusCheckbox = null;

    let settings = {
        'ajaxCallRedirectGetAgents': '',
        'acceptCallUrl': '',
        'callStatusUrl': '',
        'ajaxSaveCallUrl': '',
        'muteUrl': '',
        'unmuteUrl': '',
        'callAddNoteUrl': '',
        'clearMissedCallsUrl': ''
    };

    let panes = {
        'active': PhoneWidgetPaneActive,
        'incoming': PhoneWidgetPaneIncoming,
        'outgoing': PhoneWidgetPaneOutgoing
    };

    let queues = {
        'active': new Queue(),
        'incoming': new Queue(),
        'outgoing': new Queue()
    };

    function init(options)
    {
        console.log(options);

        Object.assign(settings, options);

        statusCheckbox = new widgetStatus('.call-status-switcher', options.updateStatusUrl);
        statusCheckbox.setStatus(options.status);
        widgetIcon.update({type: 'default', timer: false, text: null, currentCalls: null, status: statusCheckbox.getStatus() === 1});

        setCountMissedCalls(options.countMissedCalls);

        muteBtnEvent();
        transferCallBtnEvent();
        acceptCallBtnEvent();
        rejectIncomingCallEvent();
        hideIncomingCallEvent();
        callAddNoteEvent();
    }

    function removeIncomingRequest(callId) {
        queues.incoming.remove(callId);
        if (panes.incoming.getCallId() === callId) {
            panes.incoming.removeCallId();
            if (panes.incoming.isActive()) {
                refreshPanes();
            }
        }
    }

    function refreshPanes() {
        let incoming = queues.incoming.getLast();
        if (incoming !== null) {
            panes.incoming.init(incoming, queues.incoming.count(), queues.active.count());
            return;
        }

        let outgoing = queues.outgoing.getLast();
        if (outgoing !== null) {
            let obj = Object.assign({}, outgoing);
            if (obj.timeQueuePushed) {
                obj.duration = Math.floor((Date.now() - parseInt(obj.timeQueuePushed)) / 1000) + parseInt(obj.duration || 0);
            }
            panes.outgoing.init(obj);
            return;
        }

        let active = queues.active.getLast();
        if (active !== null) {
            let obj = Object.assign({}, active);
            if (obj.timeQueuePushed) {
                obj.duration = Math.floor((Date.now() - parseInt(obj.timeQueuePushed)) / 1000) + parseInt(obj.duration || 0);
            }
            if (obj.holdStartTime) {
                obj.holdDuration = Math.floor((Date.now() - parseInt(obj.holdStartTime)) / 1000);
            } else {
                obj.holdDuration = 0;
            }
            panes.active.init(obj);
            return;
        }

        widgetIcon.update({type: 'default', timer: false, text: null, currentCalls: null, status: statusCheckbox.getStatus() === 1});

        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $('#tab-phone .call-pane').addClass('is_active');
    }

    function requestIncomingCall(data) {
        console.log('incoming call');
        queues.incoming.push(data);
        panes.incoming.init(data, queues.incoming.count(), queues.active.count());
        openWidget();
        openCallTab();
    }

    function requestOutgoingCall(data) {
        console.log('outgoing call');
        if (data.callId) {
            queues.outgoing.push(data);
        }
        panes.outgoing.init(data);
        openWidget();
        openCallTab();
    }

    function requestActiveCall(data) {
        console.log('active call');
        queues.incoming.remove(data.callId);
        queues.outgoing.remove(data.callId);

        let obj = Object.assign({}, data);
        if (obj.holdDuration && obj.isHold) {
            obj.holdStartTime = Date.now() - (obj.holdDuration * 1000);
        }
        queues.active.push(obj);

        if (panes.outgoing.getCallId() === obj.callId) {
            panes.outgoing.removeCallId();
            panes.outgoing.hide();
        }

        panes.active.init(obj);
        openWidget();
        openCallTab();
    }

    function completeCall(callId)
    {
        queues.active.remove(callId);
        queues.incoming.remove(callId);
        queues.outgoing.remove(callId);

        let needRefresh = false;

        if (panes.active.getCallId() === callId) {
            panes.active.removeCallId();
            window.connection = '';
            if (panes.active.isActive()) {
                needRefresh = true;
            }
        }

        if (panes.outgoing.getCallId() === callId) {
            panes.outgoing.removeCallId();
            if (panes.outgoing.isActive()) {
                needRefresh = true;
            }
        }

        if (panes.incoming.getCallId() === callId) {
            panes.incoming.removeCallId();
            if (panes.incoming.isActive()) {
                needRefresh = true;
            }
        }

        if (needRefresh) {
            refreshPanes();
        } else {
            if (panes.incoming.isActive()) {
                panes.incoming.initWidgetIcon(queues.incoming.count(), queues.active.count());
            }
        }
    }

    function rejectIncomingCallEvent()
    {
        $(document).on('click', '#reject-incoming-call', function(e) {
            e.preventDefault();
            if (window.connection) {
                window.connection.reject();
                $.get(settings.ajaxSaveCallUrl + '?sid=' + window.connection.parameters.CallSid);
                $('#call-controls2').hide();
            }
        })
    }

    function hideIncomingCallEvent()
    {
        $(document).on('click', '#hide-incoming-call', function(e) {
            e.preventDefault();
            let callId = parseInt($(this).attr('data-call-id'));
            removeIncomingRequest(callId);
        })
    }

    function callAddNoteEvent() {
        $(document).on('click', '.call-pane-calling #active_call_add_note_submit', function (e) {
            e.preventDefault();
            let btnHtml = $(this).html();
            let callId = $(this).data('call-id');
            if (!callId) {
                createNotify('Warning', 'Call Id is undefined', 'warning');
                return false;
            }

            let value = $('.call-pane-calling #active_call_add_note').val().trim();
            if (!value) {
                createNotify('Warning', 'Note value is empty', 'warning');
                return false;
            }

            $.ajax({
                type: 'post',
                data: {note: value, callId: callId},
                url: settings.callAddNoteUrl,
                dataType: 'json',
                beforeSend: function () {
                    $('.call-pane-calling #active_call_add_note_submit').html('<i class="fa fa-spinner fa-spin" style="color: #fff;"></i>').attr('disabled', 'disabled');
                },
                success: function (data) {
                    if (data.error) {
                        createNotify('Error', data.message, 'error');
                    } else {
                        $('.call-pane-calling #active_call_add_note').val('');
                        createNotify('Success', data.message, 'success');
                    }
                },
                error: function (error) {
                    createNotify('Error', error.responseText, 'error');
                },
                complete: function () {
                    $('.call-pane-calling #active_call_add_note_submit').html(btnHtml).removeAttr('disabled');
                }
            })

        });
    }
    
    // function bindVolumeIndicators(connection)
    // {
    //     connection.on('volume', function (inputVolume, outputVolume) {
    //         volumeIndicatorsChange(inputVolume, outputVolume);
    //     });
    // }

    function volumeIndicatorsChange(inputVolume, outputVolume) {
        $('#wg-call-microphone .sound-ovf').css('right', -Math.floor(inputVolume*100) + '%');
        $('#wg-call-volume .sound-ovf').css('right', -Math.floor(outputVolume*100) + '%');
    }

    function muteBtnEvent()
    {
        let _self = this;
        $(document).on('click', '#call-pane__mute', function(e) {

            let muteBtn = $(this);

            if (conferenceBase) {

                let callSid = getActiveConnectionCallSid();

                if (callSid) {
                    if (muteBtn.attr('data-is-muted') === 'false') {
                       mute(callSid);
                    } else if (muteBtn.attr('data-is-muted') === 'true') {
                       unmute(callSid);
                    }
                } else {
                    alert('Error: Not found active Connection CallSid');
                }

            } else {
                let connection = _self.connection;
                if (muteBtn.attr('data-is-muted') === 'false') {
                    if (connection) {
                        connection.mute(true);
                        if (connection.isMuted()) {
                            muteBtn.html('<i class="fas fa-microphone-alt-slash"></i>').attr('data-is-muted', true);
                        } else {
                            new PNotify({title: "Mute", type: "error", text: "Error", hide: true});
                        }
                    }
                } else {
                    if (connection) {
                        connection.mute(false);
                        if (!connection.isMuted()) {
                            $(this).html('<i class="fas fa-microphone"></i>').attr('data-is-muted', false);
                        } else {
                            new PNotify({title: "Unmute", type: "error", text: "Error", hide: true});
                        }
                    }
                }
            }
        });
    }

    function mute(callSid) {
        let btn = panes.active.buttons.mute;
        btn.sendRequest();

        $.ajax({type: 'post', data: {'sid': callSid}, url: settings.muteUrl})
            .done(function (data) {
                if (data.error) {
                    new PNotify({title: "Mute", type: "error", text: data.message, hide: true});
                    btn.unmute();
                } else {
                    // new PNotify({title: "Hold", type: "success", text: 'Wait', hide: true});
                }
            })
            .fail(function (error) {
                new PNotify({title: "Hold", type: "error", text: data.message, hide: true});
                btn.unmute();
                console.error(error);
            })
            .always(function () {

            });
    }

    function unmute(callSid) {
        let btn = panes.active.buttons.mute;
        btn.sendRequest();

        $.ajax({type: 'post', data: {'sid': callSid}, url: settings.unmuteUrl})
            .done(function (data) {
                if (data.error) {
                    new PNotify({title: "Unmute", type: "error", text: data.message, hide: true});
                    btn.mute();
                } else {
                    // new PNotify({title: "Unmute", type: "success", text: 'Wait', hide: true});
                }
            })
            .fail(function (error) {
                new PNotify({title: "Unmute", type: "error", text: data.message, hide: true});
                btn.mute();
                console.error(error);
            })
            .always(function () {

            });
    }

    function updateConnection(conn)
    {
        this.connection = conn;
    }

    function transferCallBtnEvent()
    {
        $(document).on('click', '.call-pane-calling #wg-transfer-call', function(e) {
            e.preventDefault();
            if (!panes.active.buttons.transfer.can()) {
                return false;
            }
            initRedirectToAgent();
        });
    }

    function initRedirectToAgent()
    {
        if (settings.ajaxCallRedirectGetAgents === undefined) {
            alert('Ajax call redirect url is not set');
            return false;
        }

        let callSid = getActiveConnectionCallSid();
        if (callSid) {
            let modal = $('#web-phone-redirect-agents-modal');
            modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
            $('#web-phone-redirect-agents-modal-label').html('Transfer Call');

            $.post(settings.ajaxCallRedirectGetAgents, { sid: callSid }) // , user_id: userId
                .done(function(data) {
                    modal.find('.modal-body').html(data);
                });
        } else {
            alert('Error: Not found active connection Call SID!');
        }

        // let connection = this.connection;
        // if (connection && connection.parameters.CallSid) {
        //     let callSid = connection.parameters.CallSid;
        //     let modal = $('#web-phone-redirect-agents-modal');
        //     modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        //     $('#web-phone-redirect-agents-modal-label').html('Transfer Call');
        //
        //     $.post(options.ajaxCallRedirectGetAgents, { sid: callSid }) // , user_id: userId
        //         .done(function(data) {
        //             modal.find('.modal-body').html(data);
        //         });
        // } else {
        //     alert('Error: Not found Call connection or Call SID!');
        // }
        return false;
    }

    function refreshCallStatus(obj)
    {
        if (obj.status === 'In progress') {
            requestActiveCall(obj);
        } else if (obj.status === 'Ringing' || obj.status === 'Queued') {
            if (obj.typeId === 2) {
                requestIncomingCall(obj);
            } else if (obj.typeId === 1) {
                requestOutgoingCall(obj);
            }
        } else if (obj.status === 'Completed' || obj.isEnded) {
            completeCall(obj.callId);
        }
    }

    function openWidget()
    {
        $('.phone-widget').addClass('is_active');
        $('.js-toggle-phone-widget').removeClass('is-mirror');
    }

    function openCallTab()
    {
        $('.phone-widget__tab').removeClass('is_active');
        $('[data-toggle-tab]').removeClass('is_active');
        $('#tab-phone').addClass('is_active');
    }

    function showCallingPanel()
    {
        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $('#tab-phone .call-pane-calling').addClass('is_active');
    }

    function acceptCallBtnEvent()
    {
        $(document).on('click', '#btn-accept-call', function () {

            if (typeof device == "undefined" || device == null || (device && device._status !== 'ready')) {
                new PNotify({title: "Accept call", type: "warning", text: "Please try again after some seconds. Device is not ready.", hide: true});
                return false;
            }

            var btn = $(this);
            var fromInternal = btn.attr('data-from-internal');
            if (fromInternal !== 'false' && window.connection) {
                window.connection.accept();
                showCallingPanel();
                $('#call-controls2').hide();
            } else {
                $.ajax({
                    type: 'post',
                    url: settings.acceptCallUrl,
                    dataType: 'json',
                    data: {act: 'accept', call_id: btn.attr('data-call-id')},
                    beforeSend: function () {
                        btn.addClass('disabled');
                        btn.find('i').removeClass('fas fa-check').addClass('fa fa-spinner fa-spin');
                    },
                    success: function (data) {
                        if (data.error) {
                             new PNotify({
                                title: "Error",
                                type: "error",
                                text: data.message,
                                hide: true
                            });
                        } else {
                            // showCallingPanel();
                        }
                    },
                    complete: function () {
                        // btn.removeClass('disabled');
                        // btn.find('i').addClass('fas fa-check').removeClass('fa fa-spinner fa-spin');
                    }
                })
            };
        });
    }

    function changeStatus(status) {
        statusCheckbox.setStatus(status);
        if (!panes.active.isActive() && !panes.incoming.isActive() && !panes.outgoing.isActive()) {
            widgetIcon.update({type: 'default', timer: false, text: null, currentCalls: null, status: statusCheckbox.getStatus() === 1});
        }
    }

    function setCountMissedCalls(count) {
        $('[data-toggle-tab="tab-history"]').attr('data-missed-calls', count);
    }

    function addMissedCall() {
        let count = $('[data-toggle-tab="tab-history"]').attr('data-missed-calls');
        count++;
        $('[data-toggle-tab="tab-history"]').attr('data-missed-calls', count);
    }

    function requestClearMissedCalls() {
        $.ajax({type: 'post', data: {}, url: settings.clearMissedCallsUrl})
            .done(function (data) {
                setCountMissedCalls(data.count);
            })
            .fail(function () {
                new PNotify({title: "Clear missed calls", type: "error", text: 'Server error', hide: true});
            })
            .always(function () {

            });
    }

    function hold(data) {
        let call = queues.active.get(data.id);
        if (call === null) {
            return;
        }
        call.holdStartTime = Date.now();
        call.isHold = true;
        if (!(panes.active.getCallId() === call.callId && panes.active.isActive())) {
            return;
        }

        let btn = $('.btn-hold-call');
        btn.html('<i class="fa fa-play"></i> <span>Unhold</span>');
        btn.data('mode', 'hold');
        btn.prop('disabled', false);

        panes.active.buttons.hold.hold();
        if (panes.active.buttons.hold.isActive()) {
            panes.active.buttons.hold.enable();
        }
        widgetIcon.update({type: 'hold', timer: true, 'timerStamp': 0, text: 'on hold', currentCalls: null, status: 'online'});
    }

    function unhold(data) {
        let call = queues.active.get(data.id);
        if (call === null) {
            return;
        }
        call.holdStartTime = 0;
        call.isHold = false;
        if (!(panes.active.getCallId() === call.callId && panes.active.isActive())) {
            return;
        }

        let btn = $('.btn-hold-call');
        btn.html('<i class="fa fa-pause"></i> <span>Hold</span>');
        btn.data('mode', 'unhold');
        btn.prop('disabled', false);

        panes.active.buttons.hold.unhold();
        if (panes.active.buttons.hold.isActive()) {
            panes.active.buttons.hold.enable();
        }
        let duration = Math.floor((Date.now() - parseInt(call.timeQueuePushed)) / 1000) + parseInt(call.duration || 0);
        widgetIcon.update({type: 'inProgress', timer: true, 'timerStamp': duration, text: 'on call', currentCalls: '', status: 'online'});
    }
    
    function socket(data) {
        if (data.command === 'add_missed_call') {
            addMissedCall();
            return;
        }
        if (data.command === 'update_count_missed_calls') {
            setCountMissedCalls(data.count);
            return;
        }
        if (data.command === 'hold') {
            hold(data.call);
            return;
        }
        if (data.command === 'unhold') {
            unhold(data.call);
            return;
        }
    }

    return {
        init: init,
        volumeIndicatorsChange: volumeIndicatorsChange,
        updateConnection: updateConnection,
        refreshCallStatus: refreshCallStatus,
        panes: panes,
        requestIncomingCall: requestIncomingCall,
        requestActiveCall: requestActiveCall,
        requestOutgoingCall: requestOutgoingCall,
        changeStatus: changeStatus,
        requestClearMissedCalls: requestClearMissedCalls,
        socket: socket,
        queues: queues,
        removeIncomingRequest: removeIncomingRequest
    };
}();

(function() {

    function delay(callback, ms) {
        var timer = 0;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    }

    $("#call-pane__dial-number").on('keyup', delay(function() {
        $('.suggested-contacts').removeClass('is_active');
        let contactList = $("#contact-list-calls-ajax");
        let q = contactList.find("input[name=q]").val();
        if (q.length < 3) {
            return false;
        }
        contactList.submit();
    }, 300));

    let timeout = '';
    $('#contact-list-calls-ajax').on('beforeSubmit', function (e) {
        e.preventDefault();
        let yiiform = $(this);
        let q = yiiform.find("input[name=q]").val();
        if (q.length < 3) {
            //  new PNotify({
            //     title: "Search contacts",
            //     type: "warning",
            //     text: 'Minimum 2 symbols',
            //     hide: true
            // });
            return false;
        }
        $.ajax({
                type: yiiform.attr('method'),
                url: yiiform.attr('action'),
                data: yiiform.serializeArray(),
                dataType: 'json',
            }
        )
            .done(function(data) {
                let content = '';
                if (timeout) {
                    clearTimeout(timeout);
                }
                if (data.results.length < 1) {
                    // content += loadNotFound();
                    // timeout = setTimeout(function () {
                    //     $('.suggested-contacts').removeClass('is_active');
                    // }, 2000);
                } else {
                    $.each(data.results, function(i, item) {
                        content += loadContact(item);
                    });
                    $('.suggested-contacts').html(content).addClass('is_active');
                    $('.call-pane__dial-clear-all').addClass('is-shown')
                }
                //$('.suggested-contacts').html(content).addClass('is_active');
                //$('.call-pane__dial-clear-all').addClass('is-shown')
            })
            .fail(function () {
                new PNotify({
                    title: "Search contacts",
                    type: "error",
                    text: 'Server Error. Try again later',
                    hide: true
                });
            });
        return false;
    });

    function loadContact(contact) {
        //  type = 3 = Internal contact
        console.log(contact);
        let contactIcon = '';
        if (contact['type'] === 3) {
            contactIcon = '<div class="contact-info-card__status">' +
                '<i class="far fa-user ' + contact['user_status_class'] + ' "></i>' +
                '</div>';
        }
        let content = '<li class="calls-history__item contact-info-card call-contact-card" data-phone="' + contact['phone'] + '" data-title="' + contact['title'] + '">' +
            '<div class="collapsible-toggler">' +
            contactIcon
            + '<div class="contact-info-card__details">' +
            '<div class="contact-info-card__line history-details">' +
            '<strong class="contact-info-card__name">' + contact['name'] + '</strong>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</li>';
        return content;
    }

    // function loadNotFound() {
    //     let content = '<li class="calls-history__item contact-info-card">' +
    //         '<div class="collapsible-toggler">' +
    //         '<div class="contact-info-card__details">' +
    //         '<div class="contact-info-card__line history-details">' +
    //         '<strong class="contact-info-card__name">No results found</strong>' +
    //         '</div>' +
    //         '</div>' +
    //         '</div>' +
    //         '</li>';
    //     return content;
    // }

    $(document).on('click', "li.call-contact-card", function () {
        let phone = $(this).data('phone');
        let title = $(this).data('title');
        insertPhoneNumber(phone, title);
        $('.suggested-contacts').removeClass('is_active');
    });

})();