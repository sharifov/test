var PhoneWidgetPaneActiveBtnMute = function () {

    let btn = $('#call-pane__mute');

    function sendRequest() {
        disable();
        btn.attr('data-is-muted', null);
        btn.html('<i class="fa fa-spinner fa-spin"></i>');
    }

    function mute() {
        enable();
        btn.attr('data-is-muted', 'true');
        btn.html('<i class="fas fa-microphone-alt-slash"></i>');
    }

    function unmute() {
        enable();
        btn.attr('data-is-muted', 'false');
        btn.html('<i class="fas fa-microphone"></i>');
    }

    function show() {
        btn.show();
    }

    function hide() {
        btn.hide();
    }

    function disable() {
        btn.attr('disabled', true);
    }

    function enable() {
        btn.attr('disabled', false);
    }

    function active() {
        btn.attr('data-active', true);
    }

    function inactive() {
        btn.attr('data-active', false);
    }

    return {
        sendRequest: sendRequest,
        mute: mute,
        unmute: unmute,
        show: show,
        hide: hide,
        disable: disable,
        enable: enable,
        active: active,
        inactive: inactive
    }

}();
