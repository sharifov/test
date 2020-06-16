var PhoneWidgetPaneActiveBtnDialpad = function () {

    let btn = $('#wg-dialpad');

    function show() {
        btn.show();
    }

    function hide() {
        btn.hide();
    }

    function enable() {
        btn.attr('data-disabled', false);
    }

    function disable() {
        btn.attr('data-disabled', true);
    }

    function active() {
        btn.attr('data-active', true);
    }

    function inactive() {
        btn.attr('data-active', false);
    }

    function initActive() {
        active();
        enable();
        show();
    }

    function initInactive() {
        inactive();
        disable();
        show();
    }

    function can() {
        return btn.attr('data-active') === 'true' && btn.attr('data-disabled') === 'false';
    }

    return {
        show: show,
        hide: hide,
        enable: enable,
        disable: disable,
        active: active,
        inactive: inactive,
        initActive: initActive,
        initInactive: initInactive,
        can: can
    }

}();
