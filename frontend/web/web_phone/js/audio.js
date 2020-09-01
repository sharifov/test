(function () {

    function Incoming(queues) {
        this.queues = queues;

        this.audio = new Audio('/js/sounds/incoming_call.mp3');
        this.audio.volume = 0.3;
        this.audio.loop = true;

        this.play = function () {
            if (document.visibilityState === 'visible') {
                this.audio.play();
                return;
            }
            this.pause();
        };

        this.pause = function () {
            this.audio.pause();
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
            if (this.queues.active.count() > 0) {
                this.pause();
                return;
            }
            if (this.queues.direct.count() > 0 || this.queues.general.count() > 0) {
                this.play();
                return;
            }
            this.pause();
        };

        this.indicatorMuted = function () {
            $('#incoming-sound-indicator').attr('data-status', 0).html('<i class="fa fa-volume-off text-danger"> </i>').attr('title', 'Incoming Call - Volume OFF');
        };

        this.indicatorUnMuted = function () {
            $('#incoming-sound-indicator').attr('data-status', 1).html('<i class="fa fa-volume-up text-success"> </i>').attr('title', 'Incoming Call - Volume ON');
        };
    }

    window.phoneWidget.audio = {
        Incoming: Incoming
    }
})();