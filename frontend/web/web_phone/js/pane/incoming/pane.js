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

        setCallId(data.callId);
        let html = render(data);
        $pane.html(html);

        let project = $pane.find('.cw-project_name');
        if (data.projectName) {
            project.html(data.projectName).show();
        } else {
            project.html('').hide();
        }

        let source = $pane.find('.cw-source_name');
        if (data.sourceName) {
            source.html(data.sourceName).show();
        } else {
            source.html('').show();
        }
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

    return {
        load: load,
        show: show,
        hide: hide,
        getCallId: getCallId,
        removeCallId: removeCallId,
        isActive: isActive
    }

}();
