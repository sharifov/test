class PhoneWidgetPaneActiveBtnMute extends PhoneWidgetPaneActiveBtn {
    constructor(pane) {
        super(pane, '#call-pane__mute');
    };

    sendRequest() {
        this.disable();
        this.btn.attr('data-is-muted', null);
        this.btn.html('<i class="fa fa-spinner fa-spin"> </i>');
        return this;
    };

    mute() {
        this.enable();
        this.btn.attr('data-is-muted', 'true');
        this.btn.html('<i class="fas fa-microphone-alt-slash"> </i>');
        return this;
    };

    isMute() {
        return this.btn.attr('data-is-muted') === 'true';
    };

    unMute() {
        this.enable();
        this.btn.attr('data-is-muted', 'false');
        this.btn.html('<i class="fas fa-microphone"> </i>');
        return this;
    };

    disable() {
        this.btn.attr('disabled', true);
        return this;
    };

    enable() {
        this.btn.attr('disabled', false);
        return this;
    };
}
