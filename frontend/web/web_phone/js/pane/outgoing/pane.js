var PhoneWidgetPaneOutgoing = function () {

    /*
        data = {
           callId,
           type,
           status,
           duration,
           projectName,
           sourceName,
           to: {
                phone,
                name
           },
        }
     */
    function load(data) {
        setCallId(data.callId);
        $('.contact-info-card__label').html(data.type);
        $('.call-in-action__text').html(data.status);
        $('#cw-outgoing-name').html(data.to.name);
        $('.contact-info-card__call-type').html(data.to.phone);
        $('.call-in-action__time').html('').show().timer('remove').timer({
            format: '%M:%S',
            seconds: data.duration
        }).timer('start');
        let project = $('.call-pane-outgoing .cw-project_name');
        if (data.projectName) {
            project.html(data.projectName).show();
        } else {
            project.html('').hide();
        }
        let source = $('.call-pane-outgoing .cw-source_name');
        if (data.sourceName) {
            source.html(data.sourceName).show();
        } else {
            source.html('').show();
        }
    }

    function show() {
        $('.call-pane__call-btns').addClass('is-pending');
        $('.call-pane-initial').removeClass('is_active');
        $('.call-pane-outgoing').addClass('is_active');
    }

    function hide() {
        $('.call-pane__call-btns').removeClass('is-pending');
        $('.call-pane-outgoing').removeClass('is_active');
    }

    function setCallId(callId) {
        $('#cancel-outgoing-call').attr('data-call-id', callId);
        $('.call-pane-outgoing').attr('data-call-id', callId);
    }

    function getCallId() {
        return parseInt($('.call-pane-outgoing').attr('data-call-id'));
    }

    function removeCallId() {
        $('#cancel-outgoing-call').attr('data-call-id', '');
        $('.call-pane-outgoing').attr('data-call-id', '');
    }

    return {
        load: load,
        show: show,
        hide: hide,
        getCallId: getCallId,
        removeCallId: removeCallId
    }

}();
