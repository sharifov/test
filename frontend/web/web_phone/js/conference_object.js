(function () {
    function Conference(data) {

        this.data = data;

        this.getCountParticipants = function () {
            return this.data.participants.length;
        };

        this.getParticipants = function () {
            let participants = [];
            let timeStoragePushed = this.data.timeStoragePushed;
            this.data.participants.forEach(function (participant) {
                participant.timeStoragePushed = timeStoragePushed;
                participants.push(new Participant(participant))
            });
            return participants;
        };

        this.getDuration = function () {
            let duration = this.data.duration || 0;
            if (this.data.timeStoragePushed) {
                return Math.floor((Date.now() - parseInt(this.data.timeStoragePushed)) / 1000) + parseInt(duration);
            }
            return duration;
        };

        this.clone = function () {
            return new Conference(this.data);
        };

        this.getEventUpdateName = function () {
            return window.phoneWidget.events.conferenceUpdate + this.data.sid;
        };

        this.save = function () {
            window.phoneWidget.eventDispatcher.dispatch(this.getEventUpdateName(),{conference: this});
        };
    }

    function Participant(data) {

        this.data = data;

        this.getDuration = function () {
            let duration = this.data.duration || 0;
            if (this.data.timeStoragePushed) {
                return Math.floor((Date.now() - parseInt(this.data.timeStoragePushed)) / 1000) + parseInt(duration);
            }
            return duration;
        };

        this.clone = function () {
            return new Participant(this.data);
        };
    }

    window.phoneWidget.conference = {
        Conference: Conference
    }
})();
