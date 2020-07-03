(function () {
    function Call(data) {

        this.data = data;

        this.canTransfer = function () {
            if (!conferenceBase) {
                return this.data.status === 'In progress';
            }
            return this.data.typeId !== 3 && this.data.status === 'In progress';
        };

        this.block = function () {
            this.data.blocked = true;
        };

        this.unBlock = function () {
            this.data.blocked = false;
        };

        this.isBlocked = function () {
            return this.data.blocked === true;
        };

        this.canHoldUnHold = function () {
            return this.data.typeId !== 3 && this.data.status === 'In progress';
        };

        this.sendHoldRequest = function () {
            this.data.sentHoldRequest = true;
        };

        this.unSendHoldRequest = function () {
            this.data.sentHoldRequest = false;
        };

        this.isSentHoldRequest = function () {
            return this.data.sentHoldRequest === true;
        };

        this.sendMuteRequest = function () {
            this.data.sentMuteRequest = true;
        };

        this.unSendMuteRequest = function () {
            this.data.sentMuteRequest = false;
        };

        this.isSentMuteRequest = function () {
            return this.data.sentMuteRequest === true;
        };

        this.sendHangupRequest = function () {
            this.data.sentHangupRequest = true;
        };

        this.unSendHangupRequest = function () {
            this.data.sentHangupRequest = false;
        };

        this.isSentHangupRequest = function () {
            return this.data.sentHangupRequest === true;
        };

        this.sendReturnHoldCallRequest = function () {
            this.data.sentReturnHoldCallRequest = true;
        };

        this.unSendReturnHoldCallRequest = function () {
            this.data.sentReturnHoldCallRequest = false;
        };

        this.isSentReturnHoldCallRequest = function () {
            return this.data.sentReturnHoldCallRequest === true;
        };

        this.sendAcceptCallRequest = function () {
            this.data.sentAcceptCallRequest = true;
        };

        this.unSendAcceptCallRequest = function () {
            this.data.sentAcceptCallRequest = false;
        };

        this.isSentAcceptCallRequest = function () {
            return this.data.sentAcceptCallRequest === true;
        };

        this.getDuration = function () {
            let duration = this.data.duration || 0;
            if (this.data.timeQueuePushed) {
                return Math.floor((Date.now() - parseInt(this.data.timeQueuePushed)) / 1000) + parseInt(duration);
            }
            return duration;
        };

        this.getHoldDuration = function () {
            let duration = 0;
            if (this.data.holdStartTime) {
                duration = Math.floor((Date.now() - parseInt(this.data.holdStartTime)) / 1000);
            }
            return duration;
        };
    }

    window.callWidget.call = {
        Call: Call
    }
})();
