var PhoneWidgetCall = function () {

    this.connection = '';

    let statusCheckbox = null;

    let settings = {
        'ajaxCallRedirectGetAgents': '',
        'callStatusUrl': '',
        'ajaxSaveCallUrl': '',
        'clearMissedCallsUrl': '',
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

    let audio = {
        'incoming': new window.phoneWidget.audio.Incoming(queues, window.phoneWidget.notifier, panes.incoming, panes.outgoing)
    };

    function init(options)
    {
        callRequester.init(options);

        Object.assign(settings, options);

        statusCheckbox = new widgetStatus('.call-status-switcher', options.updateStatusUrl);
        statusCheckbox.setStatus(options.status);
        iconUpdate();

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
        callLogInfoEvent();
        callInfoEvent();
        clientInfoEvent();
        insertPhoneNumberEvent();
        hideNotificationEvent();
        muteIncomingAudioEvent();
        recordingClickEvent();
    }

    function removeIncomingRequest(callSid) {
        let isRemoved = waitQueue.remove(callSid);
        panes.queue.refresh();
        removeNotification(callSid);
        audio.incoming.refresh();

        window.phoneWidget.notifier.on(callSid);
        audio.incoming.on(callSid);

        if (isRemoved) {
            iconUpdate();
        }

        if (panes.incoming.isEqual(callSid)) {
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

        iconUpdate();

        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $('#tab-phone .call-pane').addClass('is_active');
    }

    function iconUpdate() {
        let countIncomingCalls = queues.direct.count() + queues.general.count();
        let countHoldCalls = queues.hold.count();
        let countActiveCalls = queues.active.count() + countHoldCalls;

        if (countIncomingCalls < 1 && countActiveCalls < 1) {
            widgetIcon.update({type: 'default', timer: false, text: null, currentCalls: null, status: statusCheckbox.getStatus() === 1});
            return;
        }

        let currentCalls = '';

        if (countIncomingCalls > 0 && countActiveCalls > 0) {
            currentCalls = countIncomingCalls + '+' + countActiveCalls;
        } else if (countIncomingCalls > 0 && countActiveCalls < 1) {
            currentCalls = countIncomingCalls > 1 ? countIncomingCalls : '';
        } else if (countIncomingCalls < 1 && countActiveCalls > 0) {
            if (countHoldCalls < 1) {
                let call = queues.active.getLast();
                if (call === null) {
                    console.log('Not found last active call');
                    return;
                }
                if (call.data.isHold) {
                    widgetIcon.update({type: 'hold', timer: true, 'timerStamp': call.getHoldDuration(), text: 'on hold', currentCalls: null, status: statusCheckbox.getStatus() === 1});
                    return;
                }
                widgetIcon.update({type: 'inProgress', timer: true, 'timerStamp': call.getDuration(), text: 'on call', currentCalls: null, status: statusCheckbox.getStatus() === 1});
                return;
            }
            if (countHoldCalls === 1) {
                currentCalls = '';
            } else {
                currentCalls = countActiveCalls;
            }
        }

        widgetIcon.update({type: 'incoming', timer: false, text: null, currentCalls: currentCalls, status: statusCheckbox.getStatus() === 1});
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
            addIncomingCallNotification(call, false);
            return false;
        }
        panes.queue.refresh();
        panes.queue.hide();

        if (queues.active.count() > 0 || queues.outgoing.count() > 0 || panes.incoming.isActive()) {
            addIncomingCallNotification(call, true);
        } else {
            addFirstIncomingCallNotification(call, true);
            panes.incoming.init(call, (queues.direct.count() + queues.general.count()), (queues.active.count() + queues.hold.count()));
        }

        audio.incoming.refresh();
        iconUpdate();
        openWidget();
        // openCallTab();
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
        let countIncoming = (queues.direct.count() + queues.general.count());
        let countActive = (queues.active.count() + queues.hold.count());
        if (countIncoming > 0 || countActive > 0) {
            panes.incoming.initWidgetIcon(countIncoming, countActive);
        }
        audio.incoming.refresh();
    }

    function requestActiveCall(data) {
        console.log('active call');

        waitQueue.remove(data.callSid);
        queues.outgoing.remove(data.callSid);
        removeNotification(data.callSid);

        let call = queues.active.add(data);
        if (call === null) {
            audio.incoming.refresh();
            console.log('Call is already exist in Active Queue');
            return false;
        }
        audio.incoming.refresh();

        if (panes.outgoing.isEqual(call.data.callSid)) {
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
        let incomingDeleted = removeNotification(callSid);
        audio.incoming.refresh();
        window.phoneWidget.notifier.on(callSid);
        audio.incoming.on(callSid);

        panes.queue.refresh();

        let needRefresh = false;

        if (panes.active.isEqual(callSid)) {
            panes.active.removeCallSid();
            panes.active.removeCallInProgressIndicator();
            window.connection = '';
            if (panes.active.isActive()) {
                needRefresh = true;
            }
            if (queues.wait.count() > 0) {
                if (window.phoneWidget.notifier.refresh()) {
                    openWidget();
                    panes.queue.openAllCalls();
                    audio.incoming.refresh();
                }
            }
        }

        if (panes.outgoing.isEqual(callSid)) {
            panes.outgoing.removeCallSid();
            if (panes.outgoing.isActive()) {
                needRefresh = true;
            }
        }

        if (panes.incoming.isEqual(callSid)) {
            panes.incoming.removeCallSid();
            if (panes.incoming.isActive()) {
                needRefresh = true;
            }
        }

        if (needRefresh || incomingDeleted) {
            refreshPanes();
        } else {
             if (panes.incoming.isActive()) {
                 panes.incoming.initWidgetIcon((queues.direct.count() + queues.general.count()), (queues.active.count() + queues.hold.count()));
             }
        }
        if (queues.active.count() === 0 && queues.hold.count() === 0 && (queues.direct.count() > 0 || queues.general.count() > 0)) {
            audio.incoming.refresh();
        }
    }

    function rejectIncomingCallClickEvent()
    {
        $(document).on('click', '#reject-incoming-call', function(e) {
            e.preventDefault();
            if (window.connection) {
                window.connection.reject();
                $.get(settings.ajaxSaveCallUrl + '?sid=' + window.connection.parameters.CallSid);
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
            if (panes.incoming.isEqual(callSid)) {
                panes.incoming.removeCallSid();
                if (panes.incoming.isActive()) {
                    panes.incoming.hide();
                    hideNotification(callSid);
                    refreshPanes();
                    if (queues.wait.count() > 0 && queues.active.count() === 0 && queues.outgoing.count() === 0) {
                        panes.queue.openAllCalls();
                        audio.incoming.refresh();
                    }
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
                if (muteBtn.attr('data-is-muted') === 'false') {
                    if (connection) {
                        connection.mute(true);
                        if (connection.isMuted()) {
                            panes.active.buttons.mute.mute();
                        } else {
                            new PNotify({title: "Mute", type: "error", text: "Error", hide: true});
                        }
                    }
                } else {
                    if (connection) {
                        connection.mute(false);
                        if (!connection.isMuted()) {
                            panes.active.buttons.mute.unMute();
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

        $.post(settings.ajaxCallRedirectGetAgents, { sid: callSid })
            .done(function(data) {
                modal.find('.modal-body').html(data);
            });
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
        $(document).on('click', '.btn-item-call-queue', function () {
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
        if (!panes.active.isActive() && !panes.outgoing.isActive()) {
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

        // widgetIcon.update({type: 'hold', timer: true, 'timerStamp': 0, text: 'on hold', currentCalls: null, status: 'online'});
        iconUpdate();
    }

    function unhold(callSid) {
        let call = queues.active.one(callSid);
        if (call === null) {
            return;
        }
        call.unHold();

        //widgetIcon.update({type: 'inProgress', timer: true, 'timerStamp': call.getDuration(), text: 'on call', currentCalls: '', status: 'online'});
        iconUpdate();
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

    function callLogInfoEvent() {
        $(document).on('click', '.btn-history-call-info', function(e) {
            e.preventDefault();
            let callSid = $(this).attr('data-call-sid');
            if (typeof callSid === 'undefined') {
                createNotify('Call Info', 'Not found Call Sid', 'error');
                return false;
            }
            callRequester.callLogInfo(callSid);
        });
    }

    function callInfoEvent() {
        $(document).on('click', '.pw-btn-call-info', function(e) {
            e.preventDefault();
            let callSid = $(this).attr('data-call-sid');
            if (typeof callSid === 'undefined') {
                createNotify('Call Info', 'Not found Call Sid', 'error');
                return false;
            }
            callRequester.callInfo(callSid);
        });
    }

    function clientInfoEvent() {
        $(document).on('click', '.cw-btn-client-info', function(e) {
            e.preventDefault();
            let clientId = $(this).attr('data-client-id');
            let isClient = $(this).attr('data-is-client');
            if (typeof clientId === 'undefined') {
                createNotify('Client Info', 'Not found Client ID', 'error');
                return false;
            }
            callRequester.clientInfo(clientId, isClient === 'true');
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

    function recordingClickEvent() {
        $(document).on('click', '#wg-call-record', function(e) {
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

            if (call.data.recordingDisabled) {
                sendRecordingEnableRequest(call.data.callSid);
            } else {
                sendRecordingDisableRequest(call.data.callSid);
            }
        });
    }

    function insertPhoneNumberEvent() {
        $(document).on('click', '.phone-dial-contacts', function(e) {
            e.preventDefault();
            phoneDialInsertNumber(this);
        });

        function phoneDialInsertNumber(self) {
            let data = $(self);
            let isInternal = !!data.data('user-id');
            $(".widget-phone__contact-info-modal").hide();
            $('.phone-widget__header-actions a[data-toggle-tab]').removeClass('is_active');
            $('.phone-widget__tab').removeClass('is_active');
            $('.phone-widget__header-actions a[data-toggle-tab="tab-phone"]').addClass('is_active');
            $('#tab-phone').addClass('is_active');
            insertPhoneNumber({
                'formatted': data.data('phone'),
                'title': isInternal ? '' : data.data('title'),
                'user_id': data.data('user-id'),
                'phone_to': data.data('phone'),
                'project_id': data.data('project-id'),
                'department_id': data.data('department-id'),
                'client_id': data.data('client-id'),
                'source_type_id': data.data('source-type-id'),
                'lead_id': data.data('lead-id'),
                'case_id': data.data('case-id'),
            });
        }
    }

    function hideNotificationEvent() {
        $(document).on('click', '.pw-notification-hide', function(e) {
            e.preventDefault();
            let key = $(this).attr('data-call-sid');
            hideNotification(key);
            if (panes.incoming.isEqual(key)) {
                panes.incoming.removeCallSid();
                if (panes.incoming.isActive()) {
                    panes.incoming.hide();
                    refreshPanes();
                }
            }
            audio.incoming.refresh();
            if (queues.active.count() === 0 && queues.outgoing.count() === 0 && !panes.incoming.isActive()) {
                panes.queue.openAllCalls();
            }
        });
    }

    function muteIncomingAudioEvent() {
        $(document).on('click', '#incoming-sound-indicator', function() {
            if($(this).attr('data-status') === '1') {
                audio.incoming.muted();
            } else {
                audio.incoming.unMuted();
            }
        });
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

    function sendRecordingEnableRequest(callSid) {
        let call = queues.active.one(callSid);
        if (call === null) {
            createNotify('Error', 'Not found Call on Active Queue', 'error');
            return false;
        }

        if (!call.setRecordingEnableRequestState()) {
            return false;
        }

        callRequester.recordingEnable(call);
    }

    function sendRecordingDisableRequest(callSid) {
        let call = queues.active.one(callSid);
        if (call === null) {
            createNotify('Error', 'Not found Call on Active Queue', 'error');
            return false;
        }

        if (!call.setRecordingDisableRequestState()) {
            return false;
        }

        callRequester.recordingDisable(call);
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
        if (data.command === 'addCallToHistory') {
            addCallToHistory(data);
            return;
        }
    }

    function addCallToHistory(data) {
        if ($('#tab-history .simplebar-content .history-tab-today-first-block').length > 0) {
            $('#tab-history .simplebar-content .history-tab-today-first-block').prepend(data.call);
        } else {
            let call =
            '<span class="section-separator">Today</span>' +
            '<ul class="phone-widget__list-item calls-history history-tab-today-first-block">' +
                data.call +
            '</ul>';
            $('#tab-history .simplebar-content').prepend(call);
            window.historySimpleBar.recalculate();
        }
    }

    function addFirstIncomingCallNotification(call, isShow) {
        return window.phoneWidget.notifier.addAndShowOnlyDesktop(
            call.data.callSid,
            window.phoneWidget.notifier.types.incomingCall,
            createIncomingCallNotification(call, isShow)
        );
    }

    function addIncomingCallNotification(call, isShow) {
        return window.phoneWidget.notifier.add(
            call.data.callSid,
            window.phoneWidget.notifier.types.incomingCall,
            createIncomingCallNotification(call, isShow)
        );
    }

    function createIncomingCallNotification(call, isShow) {
        return  {
            'callSid': call.data.callSid,
            'queue': call.data.queue,
            'name': call.data.contact.name,
            'phone': call.data.contact.phone,
            'project': call.data.project,
            'department': call.data.department,
            'duration': call.data.duration,
            'canCallInfo': call.data.contact.canCallInfo,
            'isInternal': call.data.isInternal,
            'fromInternal': call.data.fromInternal,
            'eventName': call.getEventUpdateName(),
            'isShow': isShow
        };
    }

    function removeNotification(key) {
        return window.phoneWidget.notifier.remove(key);
    }

    function hideNotification(key) {
        let isHide = window.phoneWidget.notifier.hide(key);
        audio.incoming.refresh();
        return isHide;
    }

    function loadCalls(data) {
        if (data.isEmpty) {
            return;
        }

        let holdExist = false;
        data.hold.forEach(function (item) {
            let call = waitQueue.add(item);
            if (call !== null) {
                holdExist = true;
                addFirstIncomingCallNotification(call, true);
            }
        });

        let lastIncomingCall = null;
        let incomingExist = false;
        data.incoming.forEach(function (item) {
            let call = waitQueue.add(item);
            if (call !== null) {
                addFirstIncomingCallNotification(call, true);
                incomingExist = true;
                lastIncomingCall = call;
            }
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

        if (data.lastActive === 'incoming' && queues.active.count() === 0 && queues.outgoing.count() === 0) {
            if (lastIncomingCall !== null) {
                panes.incoming.init(lastIncomingCall, (queues.direct.count() + queues.general.count()), (queues.active.count() + queues.hold.count()));
                openCallTab();
                audio.incoming.refresh();
                return;
            }
        }

        audio.incoming.refresh();

        if (holdExist && !activeExist && !outgoingExist && !incomingExist) {
            openWidget();
            panes.queue.openAllCalls();
            window.phoneWidget.notifier.refresh();
            audio.incoming.refresh();
            iconUpdate();
            return;
        }

        refreshPanes();
    }

    function resetQueues() {
        waitQueue.reset();
        queues.outgoing.reset();
        queues.active.reset();
        storage.conference.reset();
    }

    function updateCurrentCalls(data, userStatus) {
        statusCheckbox.setStatus(userStatus);
        iconUpdate();

        resetQueues();
        panes.queue.refresh();
        refreshPanes();
        window.phoneWidget.notifier.reset();
        loadCalls(data);
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
        callRequester: callRequester,
        completeCall: completeCall,
        updateCurrentCalls: updateCurrentCalls,
        iconUpdate: iconUpdate,
        audio: audio
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
        let dataUserId = contact.type === 3 ? contact.id : '';
        let content = '<li class="calls-history__item contact-info-card call-contact-card" data-user-id="' + dataUserId + '" data-phone="' + (dataUserId ? contact['title'] : contact['phone']) + '" data-title="' + (dataUserId ? '' : contact['title']) + '">' +
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
        let userId = $(this).data('user-id');
        insertPhoneNumber({
            'formatted': phone,
            'title': title,
            'user_id': userId,
            'phone_to': phone
        });
        $('.suggested-contacts').removeClass('is_active');
    });
})();