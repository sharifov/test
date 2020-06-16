var PhoneWidgetPaneIncoming = function () {

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
        $('#btn-accept-call').attr('data-from-internal', data.fromInternal).attr('data-call-id', data.callId);
        $('.call-pane-incoming .contact-info-card__label').html(data.type);
        $('#cw-client_name').html(data.name);
        $('.call-pane-incoming .contact-info-card__call-type').html(data.phone);
    }

    function show() {
        $('#btn-accept-call').find('i').removeClass('fa fa-spinner fa-spin');
        $('#tab-phone .call-pane-initial').removeClass('is_active');
        $('#tab-phone .call-pane-incoming').addClass('is_active');
        $('#tab-phone .call-pane-incoming .call-pane__call-btns').removeClass('is-on-call').removeClass('is-pending');
    }

    function hide() {
        $('#tab-phone .call-pane-incoming').removeClass('is_active');
    }

    function setCallId(callId) {
        $('.call-pane-incoming').attr('data-call-id', callId);
    }

    function getCallId() {
        return parseInt($('.call-pane-incoming').attr('data-call-id'));
    }

    function removeCallId() {
        return $('.call-pane-incoming').attr('data-call-id', '');
    }

    return {
        load: load,
        show: show,
        hide: hide,
        getCallId: getCallId,
        removeCallId: removeCallId
    }

}();
