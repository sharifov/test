(function () {
    function Call(data) {

        this.data = data;

        this.canTransfer = function () {
            if (!conferenceBase) {
                return this.data.status === 'In progress';
            }
            return this.data.typeId !== 3 && this.data.status === 'In progress' && !this.data.isInternal;
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
            return this.data.typeId !== 3 && this.data.status === 'In progress' && (!this.data.isInternal || (this.data.isInternal && this.data.isConferenceCreator));
        };

        this.setHoldRequestState = function () {
            if (this.isBlocked()) {
                // createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
                return false;
            }
            if (!this.canHoldUnHold()) {
                // createNotify('Error', 'Hold or UnHold disallow.', 'error');
                return false;
            }
            if (this.data.isHold) {
                createNotify('Error', 'Call is already Hold.', 'error');
                return false;
            }
            this.data.sentHoldUnHoldRequest = true;
            this.block();
            this.save();
            return true;
        };

        this.setUnHoldRequestState = function () {
            if (this.isBlocked()) {
                // createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
                return false;
            }
            if (!this.canHoldUnHold()) {
                // createNotify('Error', 'Hold or UnHold disallow.', 'error');
                return false;
            }
            if (!this.data.isHold) {
                createNotify('Error', 'Call is already UnHold.', 'error');
                return false;
            }
            this.data.sentHoldUnHoldRequest = true;
            this.block();
            this.save();
            return true;
        };

        this.unSetHoldUnHoldRequestState = function () {
            this.data.sentHoldUnHoldRequest = false;
            this.unBlock();
            this.save();
        };

        this.isSentHoldUnHoldRequestState = function () {
            return this.data.sentHoldUnHoldRequest === true;
        };

        this.hold = function () {
            this.unBlock();
            this.data.sentHoldUnHoldRequest = false;
            this.data.holdStartTime = Date.now();
            this.data.isHold = true;
            this.save();
        };

        this.unHold = function () {
            this.unBlock();
            this.data.sentHoldUnHoldRequest = false;
            this.data.holdStartTime = 0;
            this.data.isHold = false;
            this.save();
        };

        this.setMuteRequestState = function () {
            if (this.isBlocked()) {
                // createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
                return false;
            }
            if (this.data.isMute) {
                createNotify('Error', 'Call is already Mute.', 'error');
                return false;
            }
            this.data.sentMuteUnMuteRequest = true;
            this.block();
            this.save();
            return true;
        };

        this.setUnMuteRequestState = function () {
            if (this.isBlocked()) {
                // createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
                return false;
            }
            if (!this.data.isMute) {
                createNotify('Error', 'Call is already UnMute.', 'error');
                return false;
            }
            this.data.sentMuteUnMuteRequest = true;
            this.block();
            this.save();
            return true;
        };

        this.unSetMuteUnMuteRequestState = function () {
            this.data.sentMuteUnMuteRequest = false;
            this.unBlock();
            this.save();
        };

        this.isSentMuteUnMuteRequestState = function () {
            return this.data.sentMuteUnMuteRequest === true;
        };

        this.mute = function () {
            this.unBlock();
            this.data.sentMuteUnMuteRequest = false;
            this.data.isMute = true;
            this.save();
        };

        this.unMute = function () {
            this.unBlock();
            this.data.sentMuteUnMuteRequest = false;
            this.data.isMute = false;
            this.save();
        };

        this.setHangupRequestState = function () {
            if (this.isBlocked()) {
                // createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
                return false;
            }
            this.data.sentHangupRequest = true;
            this.block();
            this.save();
            return true;
        };

        this.unSetHangupRequestState = function () {
            this.data.sentHangupRequest = false;
            this.unBlock();
            this.save();
        };

        this.isSentHangupRequestState = function () {
            return this.data.sentHangupRequest === true;
        };

        this.setReturnHoldCallRequestState = function () {
            if (this.isBlocked()) {
                // createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
                return false;
            }
            if (this.data.status !== 'Hold') {
                createNotify('Error', 'Call is not in status Hold.', 'error');
                return false;
            }
            this.data.sentReturnHoldCallRequest = true;
            this.block();
            this.save();
            return true;
        };

        this.unSetReturnHoldCallRequestState = function () {
            this.data.sentReturnHoldCallRequest = false;
            this.unBlock();
            this.save();
        };

        this.isSentReturnHoldCallRequestState = function () {
            return this.data.sentReturnHoldCallRequest === true;
        };

        this.setAcceptCallRequestState = function () {
            if (this.isBlocked()) {
                // createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
                return false;
            }
            this.data.sentAcceptCallRequest = true;
            this.block();
            this.save();
            return true;
        };

        this.unSetAcceptCallRequestState = function () {
            this.data.sentAcceptCallRequest = false;
            this.unBlock();
            this.save();
        };

        this.isSentAcceptCallRequestState = function () {
            return this.data.sentAcceptCallRequest === true;
        };

        this.setAddNoteRequestState = function () {
            if (this.isBlocked()) {
                createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
                return false;
            }
            this.data.sentAddNoteRequest = true;
            this.block();
            this.save();
            return true;
        };

        this.unSetAddNoteRequestState = function () {
            this.data.sentAddNoteRequest = false;
            this.unBlock();
            this.save();
        };

        this.isSentAddNoteRequestState = function () {
            return this.data.sentAddNoteRequest === true;
        };

        this.setRejectInternalRequest = function () {
            if (this.isBlocked()) {
                createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
                return false;
            }
            this.data.sentRejectInternalRequest = true;
            this.block();
            this.save();
            return true;
        };

        this.unSetRejectInternalRequest = function () {
            this.data.sentRejectInternalRequest = false;
            this.unBlock();
            this.save();
        };

        this.isSentRejectInternalRequest = function () {
            return this.data.sentRejectInternalRequest === true;
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

        this.clone = function () {
            return new Call(this.data);
        };

        this.save = function () {
            window.phoneWidget.eventDispatcher.dispatch(this.getEventUpdateName(),{call: this});
        };

        this.getEventUpdateName = function () {
            return window.phoneWidget.events.callUpdate + this.data.callSid;
        };
    }

    window.phoneWidget.call = {
        Call: Call
    }
})();
