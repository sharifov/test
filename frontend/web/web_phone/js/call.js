var PhoneWidgetCall = function () {

    this.connection = '';

    let statusCheckbox = null;

    let settings = {
        'ajaxCallRedirectGetAgents': '',
        'callStatusUrl': '',
        'ajaxSaveCallUrl': '',
        'clearMissedCallsUrl': '',
        'currentQueueCallsUrl': '',
        'dialpadEnabled': true
    };

    let callRequester = new window.phoneWidget.requesters.CallRequester();

    let waitQueue = new window.phoneWidget.queue.Queue();

    let queues = {
        'wait': waitQueue,
        'direct': new window.phoneWidget.queue.Direct(waitQueue),
        'hold': new window.phoneWidget.queue.Hold(waitQueue),
        'general': new window.phoneWidget.queue.General(waitQueue),
        'outgoing': new window.phoneWidget.queue.Queue(),
        'active': window.phoneWidget.queue.Active()
    };

    let storage = {
        'conference': window.phoneWidget.storage.conference
    };

    let panes = {
        'active': PhoneWidgetPaneActive,
        'outgoing': PhoneWidgetPaneOutgoing,
        'incoming': PhoneWidgetPaneIncoming,
        'queue': new PhoneWidgetPaneQueue(queues)
    };

    function init(options)
    {
        callRequester.init(options);

        Object.assign(settings, options);

        statusCheckbox = new widgetStatus('.call-status-switcher', options.updateStatusUrl);
        statusCheckbox.setStatus(options.status);
        widgetIcon.update({type: 'default', timer: false, text: null, currentCalls: null, status: statusCheckbox.getStatus() === 1});

        setCountMissedCalls(options.countMissedCalls);

        panes.active.setup(options.btnHoldShow, options.btnTransferShow);

        muteBtnClickEvent();
        transferCallBtnClickEvent();
        acceptCallBtnClickEvent();
        rejectIncomingCallClickEvent();
        hideIncomingCallClickEvent();
        callAddNoteCLickEvent();
        dialpadCLickEvent();
        contactInfoClickEvent();
        holdClickEvent();
        hangupClickEvent();
        insertPhoneNumberEvent();

        loadCurrentQueueCalls();
    }

    function removeIncomingRequest(callSid) {
        waitQueue.remove(callSid);
        panes.queue.refresh();
        if (panes.incoming.getCallSid() === callSid) {
            panes.incoming.removeCallSid();
            if (panes.incoming.isActive()) {
                panes.incoming.hide();
                refreshPanes();
            }
        }
    }

    function refreshPanes() {
        PhoneWidgetContactInfo.hide();
        PhoneWidgetDialpad.hide();

        if (refreshOutgoingPane()) {
            return;
        }

        if (refreshActivePane()) {
            return;
        }

        widgetIcon.update({type: 'default', timer: false, text: null, currentCalls: null, status: statusCheckbox.getStatus() === 1});

        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $('#tab-phone .call-pane').addClass('is_active');
    }

    function refreshOutgoingPane() {
        let call = queues.outgoing.getLast();
        if (call !== null) {
            panes.outgoing.init(call);
            return true;
        }
        return false;
    }

    function refreshActivePane() {
        let call = queues.active.getLast();
        if (call !== null) {
            let conference = storage.conference.one(call.data.conferenceSid);
            if (conference !== null) {
                if (conference.getCountParticipants() > 2) {
                    panes.active.init(call, conference);
                    return true;
                }
            }
            panes.active.init(call);
            return true;
        }
        return false;
    }

    function requestIncomingCall(data) {
        console.log('incoming call');

        let call = waitQueue.add(data);
        if (call === null) {
            console.log('Call is already exist in Wait Queue');
            return false;
        }
        if (call.data.queue === 'hold') {
            console.log('hold call');
            panes.queue.refresh();
            return false;
        }
        panes.queue.refresh();
        panes.queue.hide();
        panes.incoming.init(call, (queues.direct.count() + queues.general.count()), (queues.active.count() + queues.hold.count()));
        openWidget();
        openCallTab();
    }

    function requestOutgoingCall(data) {
        console.log('outgoing call');
        let call = null;
        if (data.callSid) {
            call = queues.outgoing.add(data);
            if (call === null) {
                console.log('Call is already exist in Outgoing Queue');
                return false;
            }
        } else {
            call = new window.phoneWidget.call.Call(data);
        }
        panes.outgoing.init(call);
        openWidget();
        openCallTab();
    }

    function requestActiveCall(data) {
        console.log('active call');

        waitQueue.remove(data.callSid);
        queues.outgoing.remove(data.callSid);

        let call = queues.active.add(data);
        if (call === null) {
            console.log('Call is already exist in Active Queue');
            return false;
        }

        if (panes.outgoing.getCallSid() === call.data.callSid) {
            panes.outgoing.removeCallSid();
            panes.outgoing.hide();
        }

        if (typeof data.conference !== 'undefined' && data.conference !== null) {
            storage.conference.remove(data.conference.sid);
            let conference = storage.conference.add(data.conference);
            if (conference === null) {
                console.log('conference not added');
            } else {
                if (conference.getCountParticipants() > 2) {
                    panes.active.init(call, conference);
                    openWidget();
                    openCallTab();
                    panes.queue.refresh();
                    panes.queue.hide();
                    return;
                }
            }
        }

        panes.active.init(call);
        openWidget();
        openCallTab();
        panes.queue.refresh();
        panes.queue.hide();
    }

    function conferenceUpdate(data) {
        console.log('conference update');

        let call = null;
        data.conference.participants.forEach(function (participant) {
            if (call === null) {
                call = queues.active.one(participant.callSid);
            }
        });

        if (call === null) {
            console.log('not found call in active queue');
            return;
        }

        let conference = storage.conference.one(data.conference.sid);
        if (conference !== null) {
            let newConferenceCountParticipant = data.conference.participants.length;
            let oldConferenceCountParticipant = conference.getCountParticipants();
            if (oldConferenceCountParticipant < 3 && newConferenceCountParticipant < 3) {
                //todo
                storage.conference.remove(data.conference.sid);
                conference = storage.conference.add(data.conference);
                return;
            }
            if (oldConferenceCountParticipant < 3 && newConferenceCountParticipant > 2) {
                storage.conference.remove(data.conference.sid);
                conference = storage.conference.add(data.conference);
                panes.active.init(call, conference);
                return;
            }
            if (oldConferenceCountParticipant > 2 && newConferenceCountParticipant > 2) {
                storage.conference.update(data.conference);
                return;
            }
            if (oldConferenceCountParticipant > 2 && newConferenceCountParticipant < 3) {
                storage.conference.remove(data.conference.sid);
                storage.conference.add(data.conference);
                panes.active.init(call);
                return;
            }
            console.log('not found rule for conference update');
            return;
        }

        conference = storage.conference.add(data.conference);

        if (conference.getCountParticipants() > 2) {
            panes.active.init(call, conference);
            return;
        }

        panes.active.init(call);
    }

    function completeCall(callSid)
    {
        queues.active.remove(callSid);
        queues.outgoing.remove(callSid);
        waitQueue.remove(callSid);
        storage.conference.removeByParticipantCallSid(callSid);

        panes.queue.refresh();

        let needRefresh = false;

        if (panes.active.getCallSid() === callSid) {
            panes.active.removeCallSid();
            panes.active.removeCallInProgressIndicator();
            window.connection = '';
            if (panes.active.isActive()) {
                needRefresh = true;
            }
        }

        if (panes.outgoing.getCallSid() === callSid) {
            panes.outgoing.removeCallSid();
            if (panes.outgoing.isActive()) {
                needRefresh = true;
            }
        }

        if (panes.incoming.getCallSid() === callSid) {
            panes.incoming.removeCallSid();
            if (panes.incoming.isActive()) {
                needRefresh = true;
            }
        }

        if (needRefresh) {
            refreshPanes();
        } else {
            if (panes.incoming.isActive()) {
                panes.incoming.initWidgetIcon((queues.direct.count() + queues.general.count()), (queues.active.count() + queues.hold.count()));
            }
        }
    }

    function rejectIncomingCallClickEvent()
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

    function hideIncomingCallClickEvent()
    {
        $(document).on('click', '#hide-incoming-call', function(e) {
            e.preventDefault();
            let callSid = $(this).attr('data-call-sid');
            if (!callSid) {
                createNotify('Error', 'Not found Call SID', 'error');
                return false;
            }
            if (panes.incoming.getCallSid() === callSid) {
                panes.incoming.removeCallSid();
                if (panes.incoming.isActive()) {
                    panes.incoming.hide();
                    refreshPanes();
                }
            }
        })
    }

    function callAddNoteCLickEvent() {
        $(document).on('click', '#active_call_add_note_submit', function (e) {
            e.preventDefault();

            let callSid = $(this).data('call-sid');
            if (!callSid) {
                createNotify('Warning', 'Call SID is undefined', 'warning');
                return false;
            }

            let call = queues.active.one(callSid);
            if (call === null) {
                createNotify('Error', 'Not found Call on Active Queue', 'error');
                return false;
            }

            let $container = document.getElementById('active_call_add_note');
            let value = $container.value.trim();
            if (!value) {
                createNotify('Warning', 'Note value is empty', 'warning');
                return false;
            }

            if (!call.setAddNoteRequestState()) {
                return false;
            }

            callRequester.addNote(call, value, $container);
        });
        $(document).on('click', '#wg-add-note', function(e) {
            e.preventDefault();
            $('.additional-info.add-note').slideDown(200)
        });
        $(document).on('click', '.additional-info.add-note .additional-info__close', function() {
            $('.add-note').slideUp(150);
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

    function muteBtnClickEvent()
    {
        let _self = this;
        $(document).on('click', '.queue-separator .list_item_mute', function(e) {
            let callSid = $(this).attr('data-call-sid');
            if (!callSid) {
                createNotify('Error', 'Not found Call SID', 'error');
                return false;
            }

            let call = queues.active.one(callSid);
            if (call === null) {
                createNotify('Error', 'Not found Call on Active Queue', 'error');
                return false;
            }

            if (call.data.isMute) {
                if (!call.setUnMuteRequestState()) {
                    return false;
                }
                callRequester.unMute(call);
            } else {
                if (!call.setMuteRequestState()) {
                    return false;
                }
                callRequester.mute(call);
            }
        });
        $(document).on('click', '.call-pane-calling #call-pane__mute', function(e) {

            let muteBtn = $(this);

            if (conferenceBase) {

                let callSid = $(this).attr('data-call-sid');
                if (!callSid) {
                    createNotify('Error', 'Not found Call SID', 'error');
                    return false;
                }

                let call = queues.active.one(callSid);
                if (call === null) {
                    createNotify('Error', 'Not found Call on Active Queue', 'error');
                    return false;
                }

                if (call.data.isMute) {
                    if (!call.setUnMuteRequestState()) {
                        return false;
                    }
                    callRequester.unMute(call);
                } else {
                    if (!call.setMuteRequestState()) {
                        return false;
                    }
                    callRequester.mute(call);
                }

            } else {
                let connection = _self.connection;
                let oldBtn = $('#btn-mute-microphone');
                if (muteBtn.attr('data-is-muted') === 'false') {
                    if (connection) {
                        connection.mute(true);
                        if (connection.isMuted()) {
                            panes.active.buttons.mute.mute();
                            oldBtn.html('<i class="fa fa-microphone"></i> Unmute').removeClass('btn-success').addClass('btn-warning');
                        } else {
                            new PNotify({title: "Mute", type: "error", text: "Error", hide: true});
                        }
                    }
                } else {
                    if (connection) {
                        connection.mute(false);
                        if (!connection.isMuted()) {
                            panes.active.buttons.mute.unMute();
                            oldBtn.html('<i class="fa fa-microphone"></i> Mute').removeClass('btn-warning').addClass('btn-success');
                        } else {
                            new PNotify({title: "Unmute", type: "error", text: "Error", hide: true});
                        }
                    }
                }
            }
        });
    }

    function updateConnection(conn)
    {
        this.connection = conn;
    }

    function transferCallBtnClickEvent()
    {
        $(document).on('click', '.wg-transfer-call', function(e) {
            e.preventDefault();

            let callSid = $(this).attr('data-call-sid');
            if (!callSid) {
                createNotify('Error', 'Not found Call SID', 'error');
                return false;
            }

            let call = queues.active.one(callSid);
            if (call === null) {
                createNotify('Error', 'Not found Call on Active Queue', 'error');
                return false;
            }

            if (!call.canTransfer()) {
                // createNotify('Error', 'Disallow transfer', 'error');
                return false;
            }

            initRedirectToAgent(call.data.callSid);
        });
    }

    function initRedirectToAgent(callSid)
    {
        if (settings.ajaxCallRedirectGetAgents === undefined) {
            alert('Ajax call redirect url is not set');
            return false;
        }

        let modal = $('#web-phone-redirect-agents-modal');
        modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        $('#web-phone-redirect-agents-modal-label').html('Transfer Call');

        $.post(settings.ajaxCallRedirectGetAgents, { sid: callSid }) // , user_id: userId
            .done(function(data) {
                modal.find('.modal-body').html(data);
            });

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
    }

    function refreshCallStatus(obj)
    {
        if (obj.status === 'In progress') {
            requestActiveCall(obj);
        } else if (obj.status === 'Ringing' || obj.status === 'Queued') {
            if (parseInt(obj.typeId) === 2) {
                requestIncomingCall(obj);
            } else if (parseInt(obj.typeId) === 1) {
                requestOutgoingCall(obj);
            }
        } else if (obj.status === 'Completed' || obj.isEnded || parseInt(obj.cua_status_id) === 5) {
            completeCall(obj.callSid);
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
        $('[data-toggle-tab]').each(function( index ) {
            if ($(this).data('toggle-tab') === 'tab-phone') {
                $(this).addClass('is_active');
            }
        });
    }

    function showCallingPanel()
    {
        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $('#tab-phone .call-pane-calling').addClass('is_active');
    }

    function acceptCallBtnClickEvent()
    {
        $(document).on('click', '#btn-accept-call', function () {
            let btn = $(this);
            acceptCall(btn.attr('data-call-sid'), btn.attr('data-from-internal'));
        });
        $(document).on('click', '.call-list-item__main-action-trigger', function () {
            let btn = $(this);
            let action = $(this).attr('data-type-action');

            if (action === 'accept') {
                acceptCall(btn.attr('data-call-sid'), btn.attr('data-from-internal'));
                return false;
            }

            if (action === 'acceptInternal') {
                let call = queues.direct.one(btn.attr('data-call-sid'));
                if (call === null) {
                    createNotify('Accept Internal Call', 'Not found Call on Direct Incoming Queue', 'error');
                    return false;
                }
                acceptInternalCall(call);
                return false;
            }

            if (action === 'hangup') {
                hangup(btn.attr('data-call-sid'));
                return false;
            }

            if (action === 'return') {
                returnHoldCall(btn.attr('data-call-sid'));
                return false;
            }
            console.log('Undefined type action');
        });
    }

    function returnHoldCall(callSid)
    {
        if (!checkDevice('Return Hold Call')) {
            return false;
        }

        let call = queues.hold.one(callSid);
        if (call === null) {
            createNotify('Error', 'Not found call on Hold Queue', 'error');
            return false;
        }

        if (!call.setReturnHoldCallRequestState()) {
            return false;
        }

        callRequester.returnHoldCall(call);
    }

    function checkDevice(title) {
        if (typeof device == "undefined" || device == null || (device && device._status !== 'ready')) {
            createNotify(title, 'Please try again after some seconds. Device is not ready.', 'warning');
            return false;
        }
        return true;
    }

    function acceptCall(callSid, fromInternal)
    {
        if (!checkDevice('Accept Call')) {
            return false;
        }

        if (fromInternal !== 'false' && window.connection) {
            window.connection.accept();
            showCallingPanel();
            $('#call-controls2').hide();
        } else {
            let call = waitQueue.one(callSid);
            if (call === null) {
                createNotify('Error', 'Not found call on Wait Queue', 'error');
                return false;
            }

            if (!call.setAcceptCallRequestState()) {
                return false;
            }

            callRequester.accept(call);
        }
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
                createNotify('Clear missed calls', 'Server error', 'error');
            })
    }

    function hold(callSid) {
        let call = queues.active.one(callSid);
        if (call === null) {
            return;
        }
        call.hold();

        //todo remove after removed old widget
        if (!(panes.active.getCallSid() === call.data.callSid && panes.active.isActive())) {
            return;
        }

        window.phoneWidget.oldWidget.hold();

        widgetIcon.update({type: 'hold', timer: true, 'timerStamp': 0, text: 'on hold', currentCalls: null, status: 'online'});
    }

    function unhold(callSid) {
        let call = queues.active.one(callSid);
        if (call === null) {
            return;
        }
        call.unHold();

        //todo remove after removed old widget
        if (!(panes.active.getCallSid() === call.data.callSid && panes.active.isActive())) {
            return;
        }

        window.phoneWidget.oldWidget.unHold();

        widgetIcon.update({type: 'inProgress', timer: true, 'timerStamp': call.getDuration(), text: 'on call', currentCalls: '', status: 'online'});
    }

    function dialpadCLickEvent() {
        $(document).on('click', '.call-pane-calling #wg-dialpad', function() {
            if ($(this).attr('data-active') === 'true') {
                $('.dial-popup').slideDown(150)
            }
        });
        $(document).on('click', '.dial-popup .additional-info__close', function() {
            $('.dial-popup').slideUp(150);
        });
    }

    function contactInfoClickEvent() {
        $(document).on('click', '.call-pane__info', function() {
            $('.contact-info').slideDown(150);
        });
        $(document).on('click', '.additional-info.contact-info .additional-info__close', function() {
            $('.contact-info').slideUp(150);
        });
    }

    function hangupClickEvent() {
        $(document).on('click', '#cancel-active-call', function(e) {
            hangup($(this).attr('data-call-sid'));
        });

        $(document).on('click', '#cancel-outgoing-call', function(e) {
            e.preventDefault();
            let btn = $(this);
            let callSid = btn.attr('data-call-sid');
            if (!callSid) {
                createNotify('Hangup', 'Please try again after some seconds.', 'warning');
                return false;
            }

            let call = queues.outgoing.one(callSid);
            if (call === null) {
                createNotify('Error', 'Not found Call on Outgoing Queue', 'error');
                return false;
            }

            if (!call.setHangupRequestState()) {
                return false;
            }

            callRequester.hangupOutgoingCall(call);
        });
    }

    function holdClickEvent() {
        $(document).on('click', '#wg-hold-call', function(e) {
            if (!conferenceBase) {
                return false;
            }

            let callSid = $(this).attr('data-call-sid');
            if (!callSid) {
                createNotify('Error', 'Not found Call SID', 'error');
                return false;
            }

            let call = queues.active.one(callSid);
            if (call === null) {
                createNotify('Error', 'Not found Call on Active Queue', 'error');
                return false;
            }

            if (!call.canHoldUnHold()) {
                return false;
            }

            if (call.data.isHold) {
                sendUnHoldRequest(call.data.callSid);
            } else {
                sendHoldRequest(call.data.callSid);
            }
        });
        $(document).on('click', '.list_item_hold', function(e) {
            if (!conferenceBase) {
                return false;
            }

            let callSid = $(this).attr('data-call-sid');
            if (!callSid) {
                createNotify('Error', 'Not found Call SID', 'error');
                return false;
            }

            let call = queues.active.one(callSid);
            if (call === null) {
                createNotify('Error', 'Not found Call on Active Queue', 'error');
                return false;
            }

            if (!call.canHoldUnHold()) {
                return false;
            }

            if (call.data.isHold) {
                sendUnHoldRequest(call.data.callSid);
            } else {
                sendHoldRequest(call.data.callSid);
            }
        });
    }

    function insertPhoneNumberEvent() {
        $(document).on('click', '.phone-dial-history', function(e) {
            e.preventDefault();
            if (settings.dialpadEnabled) {
                phoneDialInsertNumber(this);
            }
        });

        $(document).on('click', '.phone-dial-contacts', function(e) {
            e.preventDefault();
            phoneDialInsertNumber(this);
        });

        function phoneDialInsertNumber(self) {
            let phone = $(self).data('phone');
            let title = $(self).data('title');
            $(".widget-phone__contact-info-modal").hide();
            $('.phone-widget__header-actions a[data-toggle-tab]').removeClass('is_active');
            $('.phone-widget__tab').removeClass('is_active');
            $('.phone-widget__header-actions a[data-toggle-tab="tab-phone"]').addClass('is_active');
            $('#tab-phone').addClass('is_active');
            insertPhoneNumber(phone, title);
        }
    }

    function sendHoldRequest(callSid) {
        let call = queues.active.one(callSid);
        if (call === null) {
            createNotify('Error', 'Not found Call on Active Queue', 'error');
            return false;
        }

        if (!call.setHoldRequestState()) {
            return false;
        }

        callRequester.hold(call);
    }

    function sendUnHoldRequest(callSid) {
        let call = queues.active.one(callSid);
        if (call === null) {
            createNotify('Error', 'Not found Call on Active Queue', 'error');
            return false;
        }

        if (!call.setUnHoldRequestState()) {
            return false;
        }

        callRequester.unHold(call);
    }

    function dialpadHide() {
        $('.dial-popup').slideUp(150);
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
            hold(data.call.sid);
            return;
        }
        if (data.command === 'unhold') {
            unhold(data.call.sid);
            return;
        }
        if (data.command === 'conferenceUpdate') {
            conferenceUpdate(data);
            return;
        }
    }

    function loadCurrentQueueCalls() {
        $(document).ready(function() {
            $.ajax({type: 'post', data: {}, url: settings.currentQueueCallsUrl})
                .done(function (data) {
                    if (data.isEmpty) {
                        return;
                    }

                    let holdExist = false;
                    data.hold.forEach(function (call) {
                        waitQueue.add(call);
                        holdExist = true;
                    });

                    let lastIncomingCall = null;
                    let incomingExist = false;
                    data.incoming.forEach(function (call) {
                        lastIncomingCall = waitQueue.add(call);
                        incomingExist = true;
                    });

                    let outgoingExist = false;
                    data.outgoing.forEach(function (call) {
                        queues.outgoing.add(call);
                        outgoingExist = true;
                    });

                    let activeExist = false;
                    data.active.forEach(function (call) {
                        queues.active.add(call);
                        activeExist = true;
                    });
                    data.conferences.forEach(function (conference) {
                        storage.conference.add(conference);
                    });

                    openWidget();
                    panes.queue.refresh();

                    if (data.lastActive === 'incoming') {
                        if (lastIncomingCall !== null) {
                            panes.incoming.init(lastIncomingCall, (queues.direct.count() + queues.general.count()), (queues.active.count() + queues.hold.count()));
                            openCallTab();
                            return;
                        }
                    }

                    if (holdExist && !activeExist && !outgoingExist && !incomingExist) {
                        openWidget();
                        panes.queue.openAllCalls();
                        return;
                    }

                    refreshPanes();
                })
                .fail(function () {
                    createNotify('Load current calls', 'Server error', 'error');
                })
        })
    }

    return {
        init: init,
        volumeIndicatorsChange: volumeIndicatorsChange,
        updateConnection: updateConnection,
        refreshCallStatus: refreshCallStatus,
        panes: panes,
        requestIncomingCall: requestIncomingCall,
        requestOutgoingCall: requestOutgoingCall,
        changeStatus: changeStatus,
        requestClearMissedCalls: requestClearMissedCalls,
        socket: socket,
        queues: queues,
        removeIncomingRequest: removeIncomingRequest,
        sendHoldRequest: sendHoldRequest,
        sendUnHoldRequest: sendUnHoldRequest,
        storage: storage,
        callRequester: callRequester
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