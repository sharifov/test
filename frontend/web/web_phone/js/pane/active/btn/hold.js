function PhoneWidgetPaneActiveBtnHold(pane) {
    let $pane = pane;
    let $btn = null;

    this.init = function () {
        $btn = $pane.find('#wg-hold-call');
        return this;
    };

    this.sendRequest = function () {
        this.disable();
        let text = 'Unhold';
        if ($btn.attr('data-mode') === 'unhold') {
            text = 'Hold';
        }
        $btn.children().html('<i class="fa fa-spinner fa-spin"></i><span>' + text + '</span>');
        return this;
    };

    this.unhold = function () {
        $btn.attr('data-mode', 'unhold');
        $btn.children().html('<i class="fa fa-pause"></i><span>Hold</span>');
        return this;
    };

    this.hold = function () {
        $btn.attr('data-mode', 'hold');
        $btn.children().html('<i class="fa fa-play"></i><span>Unhold</span>');
        return this;
    };

    this.show = function () {
        $btn.show();
        if (!conferenceBase) {
            this.disable().inactive();
        }
        return this;
    };

    this.hide = function () {
        $btn.hide();
        return this;
    };

    this.disable = function () {
        $btn.attr('data-disabled', true);
        return this;
    };

    this.enable = function () {
        $btn.attr('data-disabled', false);
        return this;
    };

    this.active = function () {
        $btn.attr('data-active', true);
        return this;
    };

    this.isActive = function () {
        return $btn.attr('data-active') === 'true';
    };

    this.inactive = function () {
        $btn.attr('data-active', false);
        return this;
    };

    this.can = function () {
        return $btn.attr('data-active') === 'true' && $btn.attr('data-disabled') === 'false';
    };

    this.initActive = function () {
        this.init().active().enable().unhold().show();
    };

    this.initInactive = function () {
        this.init().inactive().disable().unhold().show();
    };
}
