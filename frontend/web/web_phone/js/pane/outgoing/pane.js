var PhoneWidgetPaneOutgoing = function () {

    let $pane = $('.call-pane-outgoing');
    let contactInfo = PhoneWidgetContactInfo;
    let dialpad = PhoneWidgetDialpad;

    function render(data) {
        let html = '';
        let template = outgoingTpl;
        $.each(data, function (k, v) {
            html = template.split('{{' + k + '}}').join(v);
            template = html;
        });
        return html;
    }

    /*
        data = {
           callId,
           type,
           status,
           duration,
           projectName,
           sourceName,
           phone,
           name,
           contact = {
                name
           }
        }
     */
    function load(data) {
        contactInfo.load(data.contact);

        let html = render(data);
        $pane.html(html);

        $pane.find('.call-in-action__time').html('').show().timer('remove').timer({
            format: '%M:%S',
            seconds: data.duration
        }).timer('start');

        if (!data.projectName) {
            $pane.find('.cw-project_name').hide();
        }

        if (!data.sourceName) {
            $pane.find('.cw-source_name').hide();
        }

        if (!data.projectName || !data.sourceName) {
            $pane.find('.static-number-indicator__separator').hide();
        }

        setCallId(data.callId);
    }

    function show() {
        contactInfo.hide();
        dialpad.hide();

        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $pane.addClass('is_active');
    }

    function hide() {
        $pane.removeClass('is_active');
    }

    function setCallId(callId) {
        $pane.find('#cancel-outgoing-call').attr('data-call-id', callId);
        $pane.attr('data-call-id', callId);
    }

    function getCallId() {
        return parseInt($pane.attr('data-call-id'));
    }

    function removeCallId() {
        $pane.find('#cancel-outgoing-call').attr('data-call-id', '');
        $pane.attr('data-call-id', '');
    }

    function isActive() {
        return $pane.hasClass('is_active');
    }

    function init(data) {
        load(data);
        show();
        widgetIcon.update({type: 'outgoing', timer: true, 'timerStamp': data.duration, text: data.status, currentCalls: null, status: 'online'});
    }

    return {
        init: init,
        load: load,
        show: show,
        hide: hide,
        getCallId: getCallId,
        removeCallId: removeCallId,
        isActive: isActive
    }

}();
