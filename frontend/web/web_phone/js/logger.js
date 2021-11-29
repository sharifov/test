(function () {
    function Logger() {
        this.block = $('.logs-block');

        this.add = function (message, color) {
            if (typeof message === 'string') {
                let msg = '';
                if (color) {
                    msg = '<p style="color: ' + color + '">&gt;&nbsp;' + message + '</p>';
                } else {
                    msg = '<p>&gt;&nbsp;' + message + '</p>';
                }
                msg += '<p style="font-size: 9px">' + getCurrentTime() +'</p>';
                this.block.prepend(msg);
                //this.block.animate({scrollTop: this.block.prop("scrollHeight")}, 1000);
                return;
            }
            this.addError(message);
        };

        this.clear = function () {
            this.block.html('');
            //this.block.animate({scrollTop: this.block.prop("scrollHeight")}, 1000);
        };

        this.addError = function (error) {
            let msg = '<p style="font-size: 11px; font-weight: bold; color: #f41b1b">&gt;&nbsp;An error has occurred</p>';
            msg += '<p style="font-size: 9px">' + getCurrentTime() +'</p>';
            if (error.code) {
                msg += '<p>code: ' + error.code + '</p>';
            }
            if (error.name) {
                msg += '<p>name: ' + error.name + '</p>';
            }
            if (error.message) {
                msg += '<p>message: ' + error.message + '</p>';
            }
            if (error.description) {
                msg += '<p>description: ' + error.description + '</p>';
            }
            if (error.causes) {
                msg += '<p style="color: #761c19; font-weight: bold">causes: </p>';
                error.causes.forEach(function (cause, key) {
                    msg += '<p>' + (key + 1) + ': ' + cause + '</p>';
                });
            }
            if (error.solutions) {
                msg += '<p style="color: #2e6da4; font-weight: bold">solutions: </p>';
                error.solutions.forEach(function (solution, key) {
                    msg += '<p>' + (key + 1) + ': ' + solution + '</p>';
                });
            }
            this.block.prepend(msg);
           // this.block.animate({scrollTop: this.block.prop("scrollHeight")}, 1000);
        };

        function getCurrentTime() {
            const t = new Date();
            const date = ('0' + t.getDate()).slice(-2);
            const month = ('0' + (t.getMonth() + 1)).slice(-2);
            const year = t.getFullYear();
            return `${date}/${month}/${year}` + ' ' + t.getHours() + ':' + t.getMinutes() + ':' + t.getSeconds();
        }
    }

    window.phoneWidget.logger = {
        Logger: Logger
    }
})();