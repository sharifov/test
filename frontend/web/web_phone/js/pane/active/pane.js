var PhoneWidgetPaneActive = function () {

    let callSid = null;

    const containerId = 'call-pane-calling';
    let $container = $('#' + containerId);
    let $reactContainer = document.getElementById(containerId);
    let $addNoteContainer = document.getElementById('add-note');

    let contactInfo = PhoneWidgetContactInfo;
    let dialpad = PhoneWidgetDialpad;

    let buttons = {
        'hold': new PhoneWidgetPaneActiveBtnHold($container),
        'mute': new PhoneWidgetPaneActiveBtnMute($container)
    };

    let btnHoldShow = true;
    let btnTransferShow = true;

    function setup(btnHoldShowInit, btnTransferShowInit) {
        btnHoldShow = btnHoldShowInit;
        btnTransferShow = btnTransferShowInit;
    }

    function initControls() {
        buttons.hold.init();
        buttons.mute.init();
    }

    // call => window.phoneWidget.call.Call
    // conference => window.phoneWidget.conference.Conference
    function load(call, conference) {
        if (typeof conference !== 'undefined' && conference !== null) {
            $container.addClass('call-pane-calling--conference');
            ReactDOM.unmountComponentAtNode($reactContainer);
            ReactDOM.render(React.createElement(ConferencePane, {call: call, controls: getControls(call), conference: conference}), $reactContainer);
        } else {
            $container.removeClass('call-pane-calling--conference');
            ReactDOM.unmountComponentAtNode($reactContainer);
            ReactDOM.render(React.createElement(ActivePane, {call: call, controls: getControls(call)}), $reactContainer);
        }

        ReactDOM.unmountComponentAtNode($addNoteContainer);
        ReactDOM.render(React.createElement(AddNote, {call: call}), $addNoteContainer);

        $(".dialpad_btn_active").attr('data-conference-sid', call.data.conferenceSid);
        $("#call-pane__dial-number_active_dialpad").val('');

        contactInfo.load(call.data.contact);
        setCallSid(call.data.callSid);
        initControls();
    }

    function getControls(call) {
        let controls = {
            hold: {
                active: true,
                show: btnHoldShow
            },
            transfer: {
                active: true,
                show: btnTransferShow
            },
            addPerson: {active: false},
            dialpad: {active: true},
        };
        if (call.data.typeId === 3) {
            controls.hold.active = false;
            controls.transfer.active = false;
            controls.addPerson.active = false;
            controls.dialpad.active = false;
        }
        if (call.data.isInternal) {
            controls.hold.active = !!call.data.isConferenceCreator;
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
        PhoneWidgetCall.iconUpdate();
        // if (call.getHoldDuration()) {
        //     widgetIcon.update({type: 'hold', timer: true, 'timerStamp': call.getHoldDuration(), text: 'on hold', currentCalls: null, status: 'online'});
        //     return;
        // }
        // widgetIcon.update({type: 'inProgress', timer: true, 'timerStamp': call.getDuration(), text: 'on call', currentCalls: '', status: 'online'});
    }

    return {
        setup: setup,
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
