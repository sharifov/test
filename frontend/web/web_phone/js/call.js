var PhoneWidgetCall = function () {
    let statusCheckbox = null;

    let settings = {
        'ajaxCallRedirectGetAgents': '',
        'callStatusUrl': '',
        'clearMissedCallsUrl': '',
        'ajaxCreateLeadUrl': '',
        'ajaxClientGetInfoJsonUrl': '/client/ajax-get-info-json',
        'ajaxCreateLeadWithInvalidClientUrl': '',
        'redialSourceType': null,
        'getCallHistoryFromNumberUrl': '',
        'ajaxCheckRecording': '',
        'getUserByPhoneUrl': '',
        'ajaxBlackList': '',
        'ajaxCheckUserForCallUrl': '',
        'ajaxGetPhoneListIdUrl': ''
    };

    let callRequester = new window.phoneWidget.requesters.CallRequester();

    let waitQueue = new window.phoneWidget.queue.Queue();

    let queues = {
        'wait': waitQueue,
        'direct': new window.phoneWidget.queue.Direct(waitQueue),
        'hold': new window.phoneWidget.queue.Hold(waitQueue),
        'general': new window.phoneWidget.queue.General(waitQueue),
        'outgoing': new window.phoneWidget.queue.Queue(),
        'active': window.phoneWidget.queue.Active(),
        'priority': new window.phoneWidget.queue.Priority()
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

    let incomingSoundInterval = null;

    let activeCallSid = null;

    let conferenceSources = {
        "listen": {
            "name": null,
            "id": null
        },
        "barge": {
            "name": null,
            "id": null
        },
        "coach": {
            "name": null,
            "id": null
        }
    };

    let leadViewPageShortUrl = '';

    let phoneNumbers = null;

    function init(options)
    {
        callRequester.init(options);

        Object.assign(settings, options);

        conferenceSources = options.conferenceSources;
        leadViewPageShortUrl = options.leadViewPageShortUrl;
        phoneNumbers = options.phoneNumbers;

        statusCheckbox = new widgetStatus('.call-status-switcher', options.updateStatusUrl);
        statusCheckbox.setStatus(options.status);
        iconUpdate();

        setCountMissedCalls(options.countMissedCalls);

        panes.active.setup(options.btnHoldShow, options.btnTransferShow, options.canRecordingDisabled, options.canAddBlockList);

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
        addPhoneBlacklistEvent();
        createLeadEvent();
        btnTransferEvent();
        btnWarmTransferToUserEvent();
        btnTransferNumberEvent();
        btnMakeCallEvent();
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
            if (panes.active.isActive()) {
                needRefresh = true;
            }
            if (queues.wait.count() > 0 || queues.priority.count() > 0) {
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
            audio.incoming.refresh();
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
            if (window.TwilioCall) {
                window.TwilioCall.reject();
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
            let action = $(this).attr('data-type-action');
            if (action === 'acceptWarmTransfer') {
                acceptWarmTransfer(btn.attr('data-call-sid'));
                return false;
            }
            if (action === 'accept') {
                acceptCall(btn.attr('data-call-sid'), btn.attr('data-from-internal'));
                return false;
            }
            console.log('Undefined type action');
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

            if (action === 'acceptWarmTransfer') {
                acceptWarmTransfer(btn.attr('data-call-sid'));
                return false;
            }
            console.log('Undefined type action');
        });
        $(document).on('click', '.btn-item-call-priority', function () {
            acceptPriorityCall();
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
        if (window.TwilioDevice !== null && window.TwilioDevice.state === "registered") {
            return true;
        }
        createNotify(title, 'Please try again after some seconds. Device is not ready.', 'warning');
        return false;
    }

    function acceptCall(callSid, fromInternal)
    {
        if (!checkDevice('Accept Call')) {
             return false;
        }

        if (fromInternal !== 'false' && window.TwilioCall) {
            window.TwilioCall.accept();
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

    function acceptWarmTransfer(callSid)
    {
        if (!checkDevice('Accept Call')) {
            return false;
        }

        let call = waitQueue.one(callSid);
        if (call === null) {
            createNotify('Error', 'Not found call on Wait Queue', 'error');
            return false;
        }

        if (!call.setAcceptCallRequestState()) {
            return false;
        }

        callRequester.acceptWarmTransfer(call);
    }

    function acceptPriorityCall()
    {
        if (!checkDevice('Accept Call')) {
            return false;
        }

        if (queues.priority.isAccepted()) {
            return false;
        }

        callRequester.acceptPriorityCall(window.phoneWidget.notifier.keys.priorityCall);
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
            let btn = $(this);
            let btnHtml = btn.html();
            let callId = btn.data('call-id');

            $.ajax({
                type: 'post',
                url: settings.ajaxClientGetInfoJsonUrl,
                dataType: 'json',
                data: {callId: callId},
                beforeSend: function () {
                    btn.html('<i class="fa fa-spin fa-spinner" />');
                },
                success: function (data) {
                    if (data.error) {
                        createNotify('Error', data.message, 'error');
                    } else {
                        PhoneWidgetContactInfo.load(data);
                        $('.contact-info').slideDown(150);
                    }
                },
                complete: function () {
                    btn.html(btnHtml);
                },
                error: function (xhr) {
                    createNotify('Error', xhr.responseText, 'error');
                }
            });
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

    function addPhoneBlacklistEvent() {
        $(document).on('click', '.btn-add-in-blacklist', function (e) {
            e.preventDefault();
            let phone = $(this).data('phone');
            if (typeof phone === 'undefined') {
                createNotify('Call Info', 'Phone number not found', 'error');
                return false;
            }

            callRequester.addPhoneBlackList(phone);
        })
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
            let callSid = $(this).attr('data-call-sid');
            if (typeof clientId === 'undefined') {
                createNotify('Client Info', 'Not found Client ID', 'error');
                return false;
            }
            callRequester.clientInfo(clientId, callSid,isClient === 'true');
        });
    }

    function createLeadEvent() {
        $(document).on('click', '.cw-btn-create-lead', function(e) {
            e.preventDefault();
            let btn = $(this);
            let callSid = btn.attr('data-call-sid');
            let btnHtml = btn.html();
            $.ajax({
                type: 'post',
                data: {
                    callSid: callSid
                },
                url: settings.ajaxCreateLeadUrl,
                dataType: 'json',
                beforeSend: function () {
                    btn.addClass('disabled').html('<i class="fa fa-spin fa-spinner" />');
                },
                success: function (data) {
                    if (data.error) {
                        createNotify('Error', data.message, 'error');
                    } else {
                        if (data.message === 'client is invalid') {
                            createLeadWithoutCallClient(callSid);
                            return;
                        }
                        if(data.warning) {
                            createNotify('Warning', data.message, 'warning');
                        }
                        createNotify('Success', 'Lead created successfully', 'success');
                        PhoneWidgetContactInfo.load(data.contactData);
                        window.open(data.url, '_blank').focus();
                    }
                },
                complete: function () {
                    btn.removeClass('disabled').html(btnHtml);
                },
                error: function (xhr) {
                    createNotify('Error', xhr.responseText, 'error');
                }
            });
        });
    }

    function btnTransferEvent() {
        $(document).on('click', '.btn-transfer', function (e) {
            e.preventDefault();

            let obj = $(e.target);
            let objType = obj.data('type');
            let objValue = obj.data('value');

            obj.attr('disabled', true);

            let modal = $('#web-phone-redirect-agents-modal');
            modal.find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');

            let callSid = $(this).attr('data-call-sid');

            if (!callSid) {
                new PNotify({title: "Transfer call", type: "error", text: "Not found Call SID!", hide: true});
                return false;
            }

            if (!(objValue && objType)) {
                new PNotify({
                    title: "Transfer call",
                    type: "error",
                    text: "Please try again after some seconds",
                    hide: true
                });
                return false;
            }

            callRequester.transfer(callSid, objValue, objType, modal);
        });
    }

    function btnWarmTransferToUserEvent() {
        $(document).on('click', '.btn-warm-transfer-to-user', function(e) {
            e.preventDefault();

            let obj = $(e.target);
            let userId  = obj.data('user-id');
            let callSid = obj.data('call-sid');

            obj.attr('disabled', true);

            let modal = $('#web-phone-redirect-agents-modal');
            modal.find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');

            if (!callSid) {
                new PNotify({title: "Transfer call", type: "error", text: "Not found Call SID!", hide: true});
                return false;
            }

            callRequester.warmTransferToUser(callSid, userId, modal);
        });
    }

    function btnTransferNumberEvent() {
        $(document).on('click',  '.btn-transfer-number',  function (e) {
            e.preventDefault();
            let obj = $(e.target);
            let objType  = obj.data('type');
            let objValue = obj.data('value');

            obj.attr('disabled', true);

            let callSid = $(this).attr('data-call-sid');
            let to = null;
            let activeCall = getActiveCall();
            if (activeCall) {
                to = activeCall.To;
            }

            if (!callSid) {
                new PNotify({title: "Transfer call", type: "error", text: "Not found active Connection CallSid", hide: true});
                return false;
            }

            if (objValue.length < 2) {
                console.error('Error call forward param TO');
                return false;
            }

            let modal = $('#web-phone-redirect-agents-modal');
            modal.find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');

            callRequester.transferNumber(callSid, objType, to, objValue, modal);
        });
    }

    function createLeadWithoutCallClient(callSid) {
        let modalTitle = 'Create New Lead';
        var modal = $('#modal-md');
        $.ajax({
            type: 'get',
            url: settings.ajaxCreateLeadWithInvalidClientUrl,
            data: {callSid:callSid},
            dataType: 'html',
            beforeSend: function () {
                modal.find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
                modal.find('.modal-title').html(modalTitle);
                modal.modal('show');
            },
            success: function (data) {
                modal.find('.modal-body').html(data);
                modal.find('.modal-title').html(modalTitle);
                $('#preloader').addClass('d-none');
            },
            error: function () {
                new PNotify({
                    title: 'Error',
                    type: 'error',
                    text: 'Internal Server Error. Try again letter.',
                    hide: true
                });
                setTimeout(function () {
                    $('#modal-md').modal('hide');
                }, 300)
            },
        })
    }

    function holdClickEvent() {
        $(document).on('click', '#wg-hold-call', function(e) {
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
                createNotify('Error', 'Enable recording is not allowed.', 'error');
                // sendRecordingEnableRequest(call.data.callSid);
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
            let key = $(this).attr('data-key');
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

    function recordingDisable(callSid) {
        let call = queues.active.one(callSid);
        if (call === null) {
            return;
        }
        call.recordingDisable();
    }

    function recordingEnable(callSid) {
        let call = queues.active.one(callSid);
        if (call === null) {
            return;
        }
        call.recordingEnable();
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
        if (data.command === 'recordingDisable') {
            recordingDisable(data.call.sid);
            return;
        }
        if (data.command === 'recordingEnable') {
            recordingEnable(data.call.sid);
            return;
        }
        if (data.command === 'addPriorityCall') {
            addPriorityCall(data);
            return;
        }
        if (data.command === 'removePriorityCall') {
            removePriorityCall(data);
            return;
        }
        if (data.command === 'resetPriorityCall') {
            resetPriorityCall();
            return;
        }
        if (data.command === 'mute') {
            mute(data);
            return;
        }
        if (data.command === 'unmute') {
            unmute(data);
            return;
        }
    }

    function mute(data) {
        let call = queues.active.one(data.call.sid);
        if (call === null) {
            return;
        }
        call.mute();
    }

    function unmute(data) {
        let call = queues.active.one(data.call.sid);
        if (call === null) {
            return;
        }
        call.unMute();
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

    function addPriorityCallNotification(data, isShow) {
        // let key = data.project + '.' + data.department;
        return window.phoneWidget.notifier.add(
            window.phoneWidget.notifier.keys.priorityCall,
            window.phoneWidget.notifier.types.priorityCall,
            createPriorityCallNotification(data, isShow)
        );
    }

    function addFirstPriorityCallNotification(data) {
        // let key = data.project + '.' + data.department;
        return window.phoneWidget.notifier.addAndShowOnlyDesktop(
            window.phoneWidget.notifier.keys.priorityCall,
            window.phoneWidget.notifier.types.priorityCall,
            createPriorityCallNotification(data)
        );
    }

    function createPriorityCallNotification(data, isShow) {
        return  {
            'project': data.project,
            'department': data.department,
            'isShow': isShow,
            'eventName': window.phoneWidget.events.priorityQueueAccepted
        };
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
            'isShow': isShow,
            'isWarmTransfer': call.data.isWarmTransfer
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

        data.priority.forEach(function (item) {
            queues.priority.addMany(item.project, item.department, item.count);
            if (queues.active.count() > 0 || queues.outgoing.count() > 0) {
                addPriorityCallNotification(item, false);
            } else {
                addPriorityCallNotification(item, true);
            }
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

        if (activeExist) {
            window.phoneWidget.notifier.minimize();
        }

        refreshPanes();
    }

    function resetQueues() {
        waitQueue.reset();
        queues.outgoing.reset();
        queues.active.reset();
        storage.conference.reset();
        queues.priority.reset();
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

    function addPriorityCall(data) {
        console.log('add priority call');
        queues.priority.add(data.project, data.department);

         if (queues.active.count() > 0 || queues.outgoing.count() > 0) {
             addPriorityCallNotification(data, false);
         } else {
             addPriorityCallNotification(data, true);
         }

        // if (queues.active.count() > 0 || queues.outgoing.count() > 0 || panes.incoming.isActive()) {
        //     addPriorityCallNotification(data);
        // } else {
            // addFirstPriorityCallNotification(data);
            // panes.incoming.init(call, (queues.direct.count() + queues.general.count()), (queues.active.count() + queues.hold.count()));
        // }

        panes.queue.refresh();
        audio.incoming.refresh();
        //iconUpdate();
        openWidget();
    }

    function removePriorityCall(data) {
        console.log('remove priority call');
        queues.priority.remove(data.project, data.department);
        if (queues.priority.count() < 1) {
            window.phoneWidget.notifier.remove(window.phoneWidget.notifier.keys.priorityCall);
        }
        panes.queue.refresh();
        audio.incoming.refresh();
        //iconUpdate();
    }

    function resetPriorityCall() {
        console.log('reset priority call');
        queues.priority.reset();
        if (queues.priority.count() < 1) {
            window.phoneWidget.notifier.remove(window.phoneWidget.notifier.keys.priorityCall);
        }
        panes.queue.refresh();
        audio.incoming.refresh();
        //iconUpdate();
    }

    function hidePhoneNotifications() {
        window.phoneWidget.notifier.minimize();
    }

    function hangup(callSid) {
        if (!callSid) {
            createNotify('Hangup', 'Not found Call Sid', 'error');
            return false;
        }

        let call = null;

        call = queues.active.one(callSid);
        if (call === null) {
            call = queues.outgoing.one(callSid);
            if (call === null) {
                createNotify('Hangup', 'Not found Call on Active or Outgoing Queue', 'error');
                return false;
            }
        }

        if (!call.setHangupRequestState()) {
            return false;
        }

        callRequester.hangup(call);
    }

    function getActiveCall() {
        let activeCall = window.localStorage.getItem('activeCall');
        if (activeCall) {
            return JSON.parse(activeCall);
        }
        return null;
    }

    function setActiveCall(call) {
        window.localStorage.setItem('activeCall', JSON.stringify({
            'CallSid': call.parameters.CallSid,
            'To': call.parameters.To
        }));
    }

    function setActiveCallSid(callSid) {
        activeCallSid = callSid;
    }

    function removeActiveCallSid() {
        activeCallSid = null;
    }

    function getActiveCallSid() {
        return activeCallSid;
    }

    function acceptInternalCall(call) {
        if (call.isSentAcceptCallRequestState()) {
            return;
        }

        let callSid = call.data.callSid;
        let twilioCall = window.incomingTwilioCalls.get(callSid);

        if (twilioCall === null) {
            createNotify('Accept internal Call', 'Not found CallSid on Collections of IncomingTwilioCalls', 'error');
            return;
        }

        call.setAcceptCallRequestState();
        callRequester.acceptInternalCall(call, twilioCall);
    }

    function rejectInternalCall(call) {
        let callSid = call.data.callSid;
        let twilioCall = window.incomingTwilioCalls.get(callSid);

        if (twilioCall === null) {
            createNotify('Reject Internal Call', 'Not found CallSid on Collections of IncomingTwilioCalls', 'error');
            return;
        }

        call.setRejectInternalRequest();
        window.incomingTwilioCalls.remove(twilioCall.parameters.CallSid);
        twilioCall.reject();
        incomingSoundOff();
    }

    function startTimerSoundIncomingCall() {
        incomingSoundInterval = setInterval(function () {
            incomingAudio.play();
            clearInterval(incomingSoundInterval);
        }, 2500);
    }

    function incomingSoundOff() {
        clearInterval(incomingSoundInterval);
        incomingAudio.pause();
    }

    function webCallLeadRedialPriority(redialCallInfo) {
        let params = {
            params: {
                'To': redialCallInfo.phoneTo,
                'FromAgentPhone': redialCallInfo.phoneFrom,
                'c_project_id': redialCallInfo.projectId,
                'lead_id': redialCallInfo.leadId,
                'c_type': 'web-call',
                'c_user_id': window.userId,
                'c_source_type_id': settings.redialSourceType,
                'is_conference_call': 1,
                'user_identity': window.userIdentity,
                'phone_list_id': redialCallInfo.phoneListId,
                'is_redial_call': true
            }
        };

        console.log(params);
        if (window.TwilioDevice) {
            console.log('Calling ' + params.params.To + '...');
            connection = window.TwilioDevice.connect(params);
        }
    }

    function webCallLeadRedial(phone_from, phone_to, project_id, lead_id, type, c_source_type_id) {
        callRequester.webCallLeadRedial(phone_from, phone_to, project_id, lead_id, type, c_source_type_id);
    }

    function joinListen(call_sid) {
        callRequester.joinConference(conferenceSources.listen.name, conferenceSources.listen.id, call_sid);
    }

    function joinCoach(call_sid) {
        callRequester.joinConference(conferenceSources.coach.name, conferenceSources.coach.id, call_sid);
    }

    function joinBarge(call_sid) {
        callRequester.joinConference(conferenceSources.barge.name, conferenceSources.barge.id, call_sid);
    }

    function createInternalCall(toUserId, nickname) {
        callRequester.createInternalCall(toUserId, nickname);
    }

    function freeDialButton() {
        $('#btn-new-make-call').html('<i class="fas fa-phone"> </i>').attr('disabled', false);
    }

    function reserveDialButton() {
        $('#btn-new-make-call').html('<i class="fa fa-spinner fa-spin"> </i>').attr('disabled', true);
    }

    function filterCallParams(param) {
        if (typeof param === 'undefined') {
            return '';
        }
        return param;
    }

    function prepareExternalCall(data) {
        if (data.is_request_from && data.is_request_from === 'true') {
            $.ajax({
                type: 'post',
                data: {
                    'sid': data.request_call_sid
                },
                url: settings.getCallHistoryFromNumberUrl
            })
                .done(function (result) {
                    if (result.error) {
                        createNotify('Create Call', result.message, 'error');
                        freeDialButton();
                        return false;
                    }
                    data.from = result.phone;
                    createExternalCall(data);
                })
                .fail(function () {
                    createNotify('Create Call', 'Server error', 'error');
                    freeDialButton();
                });
            return false;
        }

        createExternalCall(data);
    }

    function createCall(data) {
        if (data.user_id) {
            reserveDialButton();
            createInternalCall(data.user_id, data.nickname);
            return false;
        }

        if (!data.to) {
            freeDialButton();
            new PNotify({title: "Create call", type: "error", text: 'Phone number not entered', hide: true});
            return false;
        }

        if (!(new RegExp('^[+]{1}[0-9]{9,15}$')).test(data.to)) {
            freeDialButton();
            new PNotify({title: "Create call", type: "error", text: 'Entered phone number is not correct. Phone number should contain only numbers and +', hide: true});
            return false;
        }

        reserveDialButton();

        $.ajax({
            type: 'post',
            data: {
                'phone': data.to
            },
            url: settings.getUserByPhoneUrl
        })
            .done(function (result) {
                if (result.error) {
                    createNotify('Create Call', result.message, 'error');
                    freeDialButton();
                    return false;
                }
                if (result.userId) {
                    createInternalCall(result.userId, result.nickname);
                    return false;
                }
                prepareExternalCall(data);
            })
            .fail(function () {
                createNotify('Create Call', 'Server error', 'error');
                freeDialButton();
            });
    }

    function createExternalCall(dialData) {

        if (!dialData.from) {
            createNotify('Make call', 'Not found From phone', 'error');
            freeDialButton();
            return false;
        }

        $.post(settings.ajaxCheckUserForCallUrl, {}, function(data) {

            if(data && data.is_ready) {

                $.post(settings.ajaxBlackList, {phone: dialData.to}, function(data) {
                    if (data.success) {

                        $.post(settings.ajaxCheckRecording,
                            {
                                'toPhone': filterCallParams(dialData.to),
                                'fromPhone': dialData.from,
                                'projectId': dialData.project_id,
                                'departmentId': dialData.department_id,
                                'contactId': dialData.client_id
                            }, function(dataRecording) {

                                if (dataRecording.success) {
                                    if (window.TwilioDevice) {
                                        $.post(settings.ajaxGetPhoneListIdUrl, {'phone': filterCallParams(dialData.from)}, function(data) {
                                            if (data.error) {
                                                var text = 'Error. Try again later';
                                                if (data.message) {
                                                    text = data.message;
                                                }
                                                new PNotify({title: "Make call", type: "error", text: text, hide: true});
                                                freeDialButton();
                                            } else {
                                                let params = {
                                                    params: {
                                                        'To': filterCallParams(dialData.to),
                                                        'FromAgentPhone': filterCallParams(dialData.from),
                                                        'c_project_id': filterCallParams(dialData.project_id),
                                                        'c_dep_id': filterCallParams(dialData.department_id),
                                                        'lead_id': filterCallParams(dialData.lead_id),
                                                        'case_id': filterCallParams(dialData.case_id),
                                                        'c_type': 'call-web',
                                                        'c_user_id': window.userId,
                                                        'user_identity': window.userIdentity,
                                                        'is_conference_call': 1,
                                                        'c_client_id': filterCallParams(dialData.client_id),
                                                        'c_source_type_id': filterCallParams(dialData.source_type_id),
                                                        'call_recording_disabled': dataRecording.value,
                                                        'phone_list_id': data.phone_list_id
                                                    }
                                                };
                                                console.log('create call with params:');
                                                console.log(params);
                                                connection = window.TwilioDevice.connect(params);
                                            }
                                        }, 'json');
                                    } else {
                                        freeDialButton();
                                    }
                                } else {
                                    freeDialButton();
                                    var text = 'Error. Try again later';
                                    if (dataRecording.message) {
                                        text = dataRecording.message;
                                    }
                                    new PNotify({title: "Make call", type: "error", text: text, hide: true});
                                }
                            }, 'json');

                    } else {
                        freeDialButton();
                        var text = 'Error. Try again later';
                        if (data.message) {
                            text = data.message;
                        }
                        new PNotify({title: "Make call", type: "error", text: text, hide: true});
                    }

                    phoneNumbers.clearPrimaryData();
                }, 'json');

            } else {
                if (data && data.is_on_call === true) {
                    freeDialButton();
                    window.sendCommandUpdatePhoneWidgetCurrentCalls(null, window.userId, window.generalLinePriorityIsEnabled);
                    alert('New Call Error: You have an active call. If the message is shown by mistake please contact Administrator.');
                }
                if (data && data.is_offline === true) {
                    alert('You status is offline.');
                }
                return false;
            }
        }, 'json');
    }

    function makeCallFromPhoneWidget() {
        let value = $('#call-pane__dial-number-value');
        let to = $('#call-pane__dial-number').val();
        let data = {
            'user_id': value.attr('data-user-id'),
            'from': value.attr('data-phone-from') || (phoneNumbers.getPrimaryData.value || phoneNumbers.getData.value),
            'is_request_from': value.attr('data-is-request-from'),
            'request_call_sid': value.attr('data-request-call-sid'),
            'to': to,
            'project_id': value.attr('data-project-id') || (phoneNumbers.getPrimaryData.projectId || phoneNumbers.getData.projectId),
            'department_id': value.attr('data-department-id'),
            'client_id': value.attr('data-client-id'),
            'source_type_id': value.attr('data-source-type-id'),
            'lead_id': value.attr('data-lead-id'),
            'case_id': value.attr('data-case-id')
        };

        if (data.user_id) {
            let nickname = $('#call-to-label').html();
            if (!nickname) {
                nickname = to;
            }
            data.nickname = nickname;
        }

        createCall(data);
    }

    function btnMakeCallEvent() {
        $(document).on('click', '#btn-make-call-communication-block', function (e) {
            e.preventDefault();

            let to = $('#call-to-number').val();
            let from = $('#call-from-number').val();

            if (!to) {
                createNotify('Make call', 'Please select Phone number', 'error');
                return false;
            }

            if (!from) {
                createNotify('Make call', 'Please select Phone from', 'error');
                return false;
            }

            insertPhoneNumber({
                'formatted': to,
                'title': $('#call-client-name').val(),
                'user_id': '',
                'phone_to': to,
                'phone_from': from,
                'project_id': $('#call-project-id').val(),
                'department_id': $('#call-department-id').val(),
                'client_id': $('#call-client-id').val(),
                'source_type_id': $('#call-source-type-id').val(),
                'lead_id': $('#call-lead-id').val(),
                'case_id': $('#call-case-id').val(),
            });

            $('.phone-widget__header-actions a[data-toggle-tab]').removeClass('is_active');
            $('.phone-widget__tab').removeClass('is_active');
            $('.phone-widget__header-actions a[data-toggle-tab="tab-phone"]').addClass('is_active');
            $('#tab-phone').addClass('is_active');
            $('.phone-widget').addClass('is_active');

            reserveDialButton();
            makeCallFromPhoneWidget();
        });

        $(document).on('click', '.phone-dial-history', function(e) {
            e.preventDefault();

            let data = $(this);
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
                'is_request_from': !isInternal ? true : '',
                'request_call_sid': !isInternal ? data.data('call-sid') : '',
                'project_id': data.data('project-id'),
                'department_id': data.data('department-id'),
                'client_id': data.data('client-id'),
                'source_type_id': data.data('source-type-id'),
                'lead_id': data.data('lead-id'),
                'case_id': data.data('case-id'),
            });
        });

        $(document).on('click', '#btn-new-make-call', function(e) {
            e.preventDefault();
            makeCallFromPhoneWidget();
        });
    }

    return {
        init: init,
        volumeIndicatorsChange: volumeIndicatorsChange,
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
        audio: audio,
        showCallingPanel: showCallingPanel,
        openCallTab: openCallTab,
        hidePhoneNotifications: hidePhoneNotifications,
        setActiveCall: setActiveCall,
        acceptInternalCall: acceptInternalCall,
        rejectInternalCall: rejectInternalCall,
        startTimerSoundIncomingCall: startTimerSoundIncomingCall,
        incomingSoundOff: incomingSoundOff,
        getActiveCallSid: getActiveCallSid,
        setActiveCallSid: setActiveCallSid,
        removeActiveCallSid: removeActiveCallSid,
        webCallLeadRedialPriority: webCallLeadRedialPriority,
        webCallLeadRedial: webCallLeadRedial,
        joinListen: joinListen,
        joinCoach: joinCoach,
        joinBarge: joinBarge,
        leadViewPageShortUrl: leadViewPageShortUrl,
        freeDialButton: freeDialButton
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