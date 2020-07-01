var PhoneWidgetPaneActive = function () {

    let callSid = null;

    const containerId = 'call-pane-calling';
    let $container = $('#' + containerId);

    let contactInfo = PhoneWidgetContactInfo;
    let dialpad = PhoneWidgetDialpad;

    let buttons = {
        'hold': new PhoneWidgetPaneActiveBtnHold($container),
        'transfer': new PhoneWidgetPaneActiveBtnTransfer($container),
        'addPerson': new PhoneWidgetPaneActiveBtnAddPerson($container),
        'dialpad': new PhoneWidgetPaneActiveBtnDialpad($container),
        'mute': new PhoneWidgetPaneActiveBtnMute($container)
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
            isMute,
            isListen,
            isHold,
            typeId,
            type,
            duration,
            project,
            source,
            contact = {
                name,
                phone,
            }
        }
     */
    function load(call) {
        if (call.data.typeId === 3) {
            call.data.activeControls = false;
        } else {
            call.data.activeControls = true;
        }

        let container = document.getElementById(containerId);
        ReactDOM.unmountComponentAtNode(container);
        ReactDOM.render(React.createElement(ActivePane, {call: call}), container);

        contactInfo.load(call.data.contact);
        setCallSid(call.data.callSid);
        initControls();
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

    function init(call) {
        load(call);
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
