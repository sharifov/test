class PhoneWidgetPaneActiveBtnHold extends PhoneWidgetPaneActiveBtn {
    constructor(pane) {
        super(pane, '#wg-hold-call');
    }

    sendRequest() {
        this.disable();
        let text = 'Unhold';
        if (this.btn.attr('data-mode') === 'unhold') {
            text = 'Hold';
        }
        this.btn.children().html('<i class="fa fa-spinner fa-spin"> </i><span>' + text + '</span>');
        return this;
    };

    unhold() {
        this.btn.attr('data-mode', 'unhold');
        this.btn.children().html('<i class="fa fa-pause"> </i><span>Hold</span>');
        return this;
    };

    hold() {
        this.btn.attr('data-mode', 'hold');
        this.btn.children().html('<i class="fa fa-play"> </i><span>Unhold</span>');
        return this;
    };
}
