var PhoneWidgetPaneIncoming = function () {

    let $pane = $('.call-pane-incoming');

    function render(data) {
        let html = '';
        let template = incomingTpl;
        $.each(data, function (k, v) {
            html = template.split('{{' + k + '}}').join(v);
            template = html;
        });
        return html;
    }

    /*
        data = {
            fromInternal,
            callId,
            type,
            name,
            projectName,
            sourceName,
            phone
        }
     */
    function load(data) {
        let html = render(data);
        $pane.html(html);

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
        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $pane.addClass('is_active');
    }

    function hide() {
        $pane.removeClass('is_active');
    }

    function setCallId(callId) {
        $pane.attr('data-call-id', callId);
    }

    function getCallId() {
        return parseInt($pane.attr('data-call-id'));
    }

    function removeCallId() {
        return $pane.attr('data-call-id', '');
    }

    function isActive() {
        return $pane.hasClass('is_active');
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
        getCallId: getCallId,
        removeCallId: removeCallId,
        isActive: isActive,
        initWidgetIcon: initWidgetIcon
    }

}();
