var PhoneWidgetPaneAcceptedRedial = function () {

    let callSid = null;

    const containerId = 'call-pane-accepted-redial';
    let $container = $('#' + containerId);
    let $reactContainer = document.getElementById(containerId);

    let contactInfo = PhoneWidgetContactInfo;
    let dialpad = PhoneWidgetDialpad;

    function load(call) {
        ReactDOM.unmountComponentAtNode($reactContainer);
        ReactDOM.render(React.createElement(AcceptedRedialPane, {call: call}), $reactContainer);

        setCallSid(call.callSid);
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

    function init(data) {
        load(data);
        show();
    }

    return {
        init: init,
        load: load,
        show: show,
        hide: hide,
        isEqual: isEqual,
        removeCallSid: removeCallSid,
        isActive: isActive
    }

}();
