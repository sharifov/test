(function () {
    function Logger() {
        this.block = $('.logs-block');

        this.log = function (message) {
            let msg = '<p>&gt;&nbsp;' + message + '</p>';
            this.block.append(msg);
            this.block.animate({scrollTop: this.block.prop("scrollHeight")}, 1000);
        }

        this.clearLog = function () {
            this.block.html('');
            this.block.animate({scrollTop: this.block.prop("scrollHeight")}, 1000);
        }
    }

    window.phoneWidget.logger = {
        Logger: Logger
    }
})()