function PhoneWidgetPaneActiveBtnMute (pane) {
    let $pane = pane;
    let $btn = null;

    this.init = function () {
        $btn = $pane.find('#call-pane__mute');
        return this;
    };
    
    this.sendRequest = function () {
        this.disable();
        $btn.attr('data-is-muted', null);
        $btn.html('<i class="fa fa-spinner fa-spin"></i>');
        return this;
    };

    this.mute = function () {
        this.enable();
        $btn.attr('data-is-muted', 'true');
        $btn.html('<i class="fas fa-microphone-alt-slash"></i>');
        return this;
    };

    this.unmute = function () {
        this.enable();
        $btn.attr('data-is-muted', 'false');
        $btn.html('<i class="fas fa-microphone"></i>');
        return this;
    };

    this.show = function () {
        $btn.show();
        return this;
    };

    this.hide = function () {
        $btn.hide();
        return this;
    };

    this.disable = function () {
        $btn.attr('disabled', true);
        return this;
    };

    this.enable = function () {
        $btn.attr('disabled', false);
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
}
