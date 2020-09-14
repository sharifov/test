(function () {

    function Incoming(queues, notifier, incomingPane, outgoingPane) {
        this.queues = queues;
        this.notifier = notifier;
        this.incomingPane = incomingPane;
        this.outgoingPane = outgoingPane;

        this.audio = new Audio('/js/sounds/incoming_call.mp3');
        this.audio.volume = 0.3;
        this.audio.loop = true;

        this.isOn = true;
        this.offKey = null;

        this.play = function () {
            if (document.visibilityState === 'visible') {
                this.audio.play();
                return;
            }
            this.stop();
        };

        this.stop = function () {
            this.audio.pause();
            this.audio.currentTime = 0;
        };

        this.muted = function () {
            this.audio.muted = true;
            this.indicatorMuted();
        };

        this.unMuted = function () {
            this.audio.muted = false;
            this.indicatorUnMuted();
        };

        this.isMuted = function () {
            return this.audio.muted === true;
        };

        this.refresh = function () {
            if (!this.isOn) {
                this.stop();
                return;
            }
            if (this.queues.active.count() > 0) {
                this.stop();
                return;
            }
            if (this.outgoingPane.isActive()) {
                this.stop();
                return;
            }
            if (this.incomingPane.isActive()) {
                this.play();
                return;
            }
            if (this.notifier.getVisibleNotifications().length > 0) {
                this.play();
                return;
            }
            this.stop();
        };

        this.indicatorMuted = function () {
            $('#incoming-sound-indicator').attr('data-status', 0).html('<i class="fa fa-volume-off text-danger"> </i>').attr('title', 'Incoming Call - Volume OFF');
        };

        this.indicatorUnMuted = function () {
            $('#incoming-sound-indicator').attr('data-status', 1).html('<i class="fa fa-volume-up text-success"> </i>').attr('title', 'Incoming Call - Volume ON');
        };

        this.on = function (key) {
            if (this.isOff() && this.offKey !== key) {
                return;
            }
            this.isOn = true;
        };

        this.off = function (key) {
            this.isOn = false;
            this.offKey = key;
        };

        this.isOff = function () {
            return this.isOn === false;
        };
    }

    window.phoneWidget.audio = {
        Incoming: Incoming
    }
})();