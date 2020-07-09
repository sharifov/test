(function () {
    function ConferenceStorage() {

        this.conferences = [];

        function init(data) {
            data.timeStoragePushed = Date.now();
        }

        this.add = function (data) {
            if (this.getIndex(data.sid) !== null) {
                return null;
            }
            init(data);
            this.conferences.unshift(data);
            return new window.phoneWidget.conference.Conference(data);
        };

        this.remove = function (sid) {
            let index = this.getIndex(sid);
            if (index !== null) {
                this.conferences.splice(index, 1);
            }
        };

        this.getIndex = function (sid) {
            let index = null;
            this.conferences.forEach(function (conference, i) {
                if (conference.sid === sid) {
                    index = i;
                    return false;
                }
            });
            return index;
        };

        this.removeByParticipantCallSid = function (sid) {
            let index = this.getIndexByParticipantCallSid(sid);
            if (index !== null) {
                this.conferences.splice(index, 1);
            }
        };

        this.getIndexByParticipantCallSid = function (callSid) {
            let index = null;
            this.conferences.forEach(function (conference, i) {
                conference.participants.forEach(function (participant) {
                    if (participant.callSid === callSid) {
                        index = i;
                    }
                });
            });
            return index;
        };

        this.one = function (sid) {
            let index = this.getIndex(sid);
            if (index !== null) {
                return new window.phoneWidget.conference.Conference(this.conferences[index]);
            }
            return null;
        };

        this.update = function (conference) {
            this.remove(conference.sid);
            let conf = this.add(conference);
            if (conf === null) {
                console.log('conference not added to storage');
                return;
            }
            conf.dispatchEvent();
        };

        this.showAll = function () {
            this.conferences.forEach(function (conference) {
                console.log(conference);
            });
        };
    }

    window.phoneWidget.storage.conference = new ConferenceStorage();
})();