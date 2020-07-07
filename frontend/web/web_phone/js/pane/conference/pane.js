var PhoneWidgetPaneConference = function () {

    let callSid = null;

    const containerId = 'call-pane-calling';
    let $container = $('#' + containerId);
    let $reactContainer = document.getElementById(containerId);

    let contactInfo = PhoneWidgetContactInfo;
    let dialpad = PhoneWidgetDialpad;

    let buttons = {
        'hold': new PhoneWidgetPaneActiveBtnHold($container),
        'mute': new PhoneWidgetPaneActiveBtnMute($container)
    };

    function initControls() {
        buttons.hold.init();
        buttons.mute.init();
    }

    // call => window.phoneWidget.call.Call
    function load(call, conference) {
        $container.addClass('call-pane-calling--conference');
        ReactDOM.unmountComponentAtNode($reactContainer);
        ReactDOM.render(React.createElement(ConferencePane, {call: call, controls: getControls(call), conference: conference}), $reactContainer);

        contactInfo.load(call.data.contact);
        setCallSid(call.data.callSid);
        initControls();
    }

    function getControls(call) {
        let controls = {
            hold: {active: true},
            transfer: {active: true},
            addPerson: {active: false},
            dialpad: {active: false},
        };
        if (call.data.typeId === 3) {
            controls.hold.active = false;
            controls.transfer.active = false;
            controls.addPerson.active = false;
            controls.dialpad.active = false;
        }
        if (!conferenceBase) {
            controls.hold.active = false;
            controls.transfer.active = true;
            controls.addPerson.active = false;
            controls.dialpad.active = false;
        }
        return controls;
    }

    function setCallSid(sid) {
        callSid = sid;
    }

    function getCallSid() {
        return callSid;
    }

    function removeCallSid() {
        callSid = null;
    }

    function show() {
        contactInfo.hide();
        dialpad.hide();

        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $container.addClass('is_active');
        addCallInProgressIndicator();
    }

    function hide() {
        $container.removeClass('is_active');
        removeCallInProgressIndicator();
    }

    function addCallInProgressIndicator() {
        $('[data-toggle-tab="tab-phone"]').attr('data-call-in-progress', true);
    }

    function removeCallInProgressIndicator() {
        $('[data-toggle-tab="tab-phone"]').attr('data-call-in-progress', false);
    }

    function isActive() {
        return $container.hasClass('is_active');
    }

    function init(call, conference) {
        load(call, conference);
        show();
        if (call.getHoldDuration()) {
            widgetIcon.update({type: 'hold', timer: true, 'timerStamp': call.getHoldDuration(), text: 'on hold', currentCalls: null, status: 'online'});
            return;
        }
        widgetIcon.update({type: 'inProgress', timer: true, 'timerStamp': call.getDuration(), text: 'on call', currentCalls: '', status: 'online'});
    }

    return {
        buttons: buttons,
        init: init,
        load: load,
        show: show,
        hide: hide,
        getCallSid: getCallSid,
        removeCallSid: removeCallSid,
        isActive: isActive,
        removeCallInProgressIndicator: removeCallInProgressIndicator
    }

}();
