var PhoneWidgetDialpad = function () {

    let $pane = $('.dial-popup');

    function hide() {
        $pane.hide();
    }

    return {
        hide: hide
    }
}();
