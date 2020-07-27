class PhoneWidgetPaneActiveBtn {
    constructor(pane, id) {
        this.pane = pane;
        this.btn = null;
        this.id = id;
    };

    init() {
        this.btn = this.pane.find(this.id);
        return this;
    };

    show() {
        this.btn.show();
        return this;
    };

    hide() {
        this.btn.hide();
        return this;
    };

    enable() {
        this.btn.attr('data-disabled', false);
        return this;
    };

    disable() {
        this.btn.attr('data-disabled', true);
        return this;
    };

    active() {
        this.btn.attr('data-active', true);
        return this;
    };

    isActive() {
        return this.btn.attr('data-active') === 'true';
    };

    inactive() {
        this.btn.attr('data-active', false);
        return this;
    };
}
