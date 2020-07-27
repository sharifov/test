(function () {
    function OldWidget() {
        this.hold = function () {
            let btn = $('.btn-hold-call');
            btn.html('<i class="fa fa-play"> </i> <span>Unhold</span>');
            btn.attr('data-mode', 'hold');
            btn.prop('disabled', false);
        };

        this.unHold = function () {
            let btn = $('.btn-hold-call');
            btn.html('<i class="fa fa-pause"> </i> <span>Hold</span>');
            btn.attr('data-mode', 'unhold');
            btn.prop('disabled', false);
        };
    }
    window.phoneWidget.oldWidget = new OldWidget();
})();
