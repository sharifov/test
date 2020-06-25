var PhoneWidgetPaneActive = function () {

    let state = {
        'callSid': null,
        'callId': null,
        'typeId': null,
        'isHold': null,
        'isMute': null,
        'isListen': null
    };

    let $pane = $('.call-pane-calling');
    let contactInfo = PhoneWidgetContactInfo;
    let dialpad = PhoneWidgetDialpad;

    let buttons = {
        'hold': new PhoneWidgetPaneActiveBtnHold($pane),
        'transfer': new PhoneWidgetPaneActiveBtnTransfer($pane),
        'addPerson': new PhoneWidgetPaneActiveBtnAddPerson($pane),
        'dialpad': new PhoneWidgetPaneActiveBtnDialpad($pane),
        'mute': new PhoneWidgetPaneActiveBtnMute($pane)
    };

    function initControls() {
        buttons.hold.init();
        buttons.transfer.init();
        buttons.addPerson.init();
        buttons.dialpad.init();
        buttons.mute.init();
    }

    /*
        data = {
            callSid,
            callId,
            isMute,
            isListen,
            isHold,
            typeId,
            type,
            phone,
            name,
            duration,
            projectName,
            sourceName,
            contact = {
                name
            }
        }
     */
    function load(data) {
        if (data.typeId === 3) {
            data.activeControls = false;
        } else {
            data.activeControls = true;
        }

        ReactDOM.render(
            React.createElement(ActivePane, data),
            document.getElementById('call-pane-calling')
        );

        contactInfo.load(data.contact);
        Object.assign(state, data);
        initControls();
    }

    function getCallId() {
        return state.callId;
    }

    function removeCallId() {
        state.callId = null;
    }

    function getCallSid() {
        return state.callSid;
    }

    function removeCallSid() {
        state.callSid = null;
    }

    function show() {
        contactInfo.hide();
        dialpad.hide();

        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $pane.addClass('is_active');
        $('[data-toggle-tab="tab-phone"]').attr('data-call-in-progress', true);
    }

    function hide() {
        $pane.removeClass('is_active');
        removeCallInProgressIndicator();
    }

    function removeCallInProgressIndicator() {
        $('[data-toggle-tab="tab-phone"]').attr('data-call-in-progress', false);
    }

    function isActive() {
        return $pane.hasClass('is_active');
    }

    function canTransfer() {
        return state.typeId !== 3;
    }

    function canHold() {
        return state.typeId !== 3 && !state.isHold;
    }

    function isMute() {
        return state.isMute === true;
    }

    function mute() {
        state.isMute = true;
        buttons.mute.mute();
    }

    function unMute() {
        state.isMute = false;
        buttons.mute.unMute();
    }

    function init(data) {
        load(data);
        show();
        if (data.holdDuration) {
            widgetIcon.update({type: 'hold', timer: true, 'timerStamp': data.holdDuration, text: 'on hold', currentCalls: null, status: 'online'});
            return;
        }
        widgetIcon.update({type: 'inProgress', timer: true, 'timerStamp': data.duration, text: 'on call', currentCalls: '', status: 'online'});
    }

    return {
        buttons: buttons,
        canTransfer: canTransfer,
        canHold: canHold,
        isMute: isMute,
        init: init,
        load: load,
        show: show,
        hide: hide,
        getCallId: getCallId,
        removeCallId: removeCallId,
        getCallSid: getCallSid,
        removeCallSid: removeCallSid,
        isActive: isActive,
        removeCallInProgressIndicator: removeCallInProgressIndicator,
        mute: mute,
        unMute: unMute
    }

}();
