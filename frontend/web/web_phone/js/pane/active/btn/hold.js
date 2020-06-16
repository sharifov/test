var PhoneWidgetPaneActiveBtnHold = function () {

    let btn = $('#wg-hold-call');

    function sendRequest() {
        disable();
        let text = 'Unhold';
        if (btn.attr('data-mode') === 'unhold') {
            text = 'Hold';
        }
        btn.children().html('<i class="fa fa-spinner fa-spin"></i><span>' + text + '</span>');
    }

    function hold() {
        btn.attr('data-mode', 'unhold');
        btn.children().html('<i class="fa fa-pause"></i><span>Hold</span>');
    }

    function unhold() {
        btn.attr('data-mode', 'hold');
        btn.children().html('<i class="fa fa-play"></i><span>Unhold</span>');
    }

    function show() {
        btn.show();
    }

    function hide() {
        btn.hide();
    }

    function disable() {
        btn.attr('data-disabled', true);
    }

    function enable() {
        btn.attr('data-disabled', false);
    }

    function active() {
        btn.attr('data-active', true);
    }

    function inactive() {
        btn.attr('data-active', false);
    }

    function can() {
        return btn.attr('data-active') === 'true' && btn.attr('data-disabled') === 'false';
    }

    function initActive() {
        active();
        enable();
        unhold();
        show();
    }

    function initInactive() {
        inactive();
        disable();
        unhold();
        show();
    }

    return {
        sendRequest: sendRequest,
        hold: hold,
        unhold: unhold,
        show: show,
        hide: hide,
        enable: enable,
        disable: disable,
        initActive: initActive,
        initInactive: initInactive,
        can: can
    }

}();
