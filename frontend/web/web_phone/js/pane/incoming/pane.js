var PhoneWidgetPaneIncoming = function () {

    let callSid = null;

    const containerId = 'call-pane-incoming';
    let $container = $('#' + containerId);
    let $reactContainer = document.getElementById(containerId);

    let contactInfo = PhoneWidgetContactInfo;
    let dialpad = PhoneWidgetDialpad;

    // call => window.phoneWidget.call.Call
    function load(call) {
        contactInfo.load(call.data.contact);

        ReactDOM.unmountComponentAtNode($reactContainer);
        ReactDOM.render(React.createElement(IncomingPane, {call: call}), $reactContainer);

        setCallSid(call.data.callSid);
    }

    function show() {
        contactInfo.hide();
        dialpad.hide();

        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $container.addClass('is_active');
    }

    function hide() {
        ReactDOM.unmountComponentAtNode($reactContainer);
        $container.removeClass('is_active');
    }

    function setCallSid(sid) {
        callSid = sid;
    }

    function isEqual(sid) {
        return callSid === sid;
    }

    function removeCallSid() {
        callSid = null;
    }

    function isActive() {
        return $container.hasClass('is_active');
    }

    function init(data, countIncoming, countActive) {
        load(data);
        show();
        initWidgetIcon(countIncoming, countActive);
    }

    function initWidgetIcon(countIncoming, countActive) {
        let currentCalls = '';
        if (countActive) {
            currentCalls = countIncoming + '+' + countActive;
        } else {
            if (countIncoming > 1) {
                currentCalls = countIncoming;
            }
        }
        widgetIcon.update({type: 'incoming', timer: false, text: null, currentCalls: currentCalls, status: 'online'});
    }

    return {
        init: init,
        load: load,
        show: show,
        hide: hide,
        isEqual: isEqual,
        removeCallSid: removeCallSid,
        isActive: isActive,
        initWidgetIcon: initWidgetIcon
    }

}();
