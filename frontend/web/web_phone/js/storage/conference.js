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

        this.one = function (sid) {
            let index = this.getIndex(sid);
            if (index !== null) {
                return new window.phoneWidget.conference.Conference(this.conferences[index]);
            }
            return null;
        };

        this.showAll = function () {
            this.conferences.forEach(function (conference) {
                console.log(conference);
            });
        };
    }

    window.phoneWidget.storage.conference = new ConferenceStorage();
})();