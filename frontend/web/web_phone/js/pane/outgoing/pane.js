var PhoneWidgetPaneOutgoing = function () {

    let callSid = null;

    const containerId = 'call-pane-outgoing';
    let $container = $('#' + containerId);

    let contactInfo = PhoneWidgetContactInfo;
    let dialpad = PhoneWidgetDialpad;

    /*
        data = {
           callSid,
           type,
           status,
           duration,
           project,
           source,
           contact = {
                name,
                phone
           }
        }
     */
    function load(call) {
        contactInfo.load(call.data.contact);

        let container = document.getElementById(containerId);
        ReactDOM.unmountComponentAtNode(container);
        ReactDOM.render(React.createElement(OutgoingPane, {call: call}), container);

        setCallSid(call.data.callSid);
    }

    function show() {
        contactInfo.hide();
        dialpad.hide();

        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $container.addClass('is_active');
    }

    function hide() {
        $container.removeClass('is_active');
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

    function isActive() {
        return $container.hasClass('is_active');
    }

    function init(call) {
        load(call);
        show();
        widgetIcon.update({type: 'outgoing', timer: true, 'timerStamp': call.getDuration(), text: call.data.status, currentCalls: null, status: 'online'});
    }

    return {
        init: init,
        load: load,
        show: show,
        hide: hide,
        getCallSid: getCallSid,
        removeCallSid: removeCallSid,
        isActive: isActive
    }

}();
