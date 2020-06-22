function PhoneWidgetPaneActiveBtnDialpad (pane) {
    let $pane = pane;
    let $btn = null;

    this.init = function () {
        $btn = $pane.find('#wg-dialpad');
        return this;
    };
    
    this.show = function () {
        this.inactive().disable();
        $btn.show();
        return this;
    };

    this.hide = function () {
        $btn.hide();
        return this;
    };

    this.enable = function () {
        $btn.attr('data-disabled', false);
        return this;
    };

    this.disable = function () {
        $btn.attr('data-disabled', true);
        return this;
    };

    this.active = function () {
        $btn.attr('data-active', true);
        return this;
    };

    this.inactive = function () {
        $btn.attr('data-active', false);
        return this;
    };

    this.initActive = function () {
        this.init().active().enable().show();
    };

    this.initInactive = function () {
        this.init().inactive().disable().show();
    };

    this.can = function () {
        return $btn.attr('data-active') === 'true' && $btn.attr('data-disabled') === 'false';
    };
}
