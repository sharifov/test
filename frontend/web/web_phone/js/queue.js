function Queue() {

    this.calls = [];

    this.push = function (data) {
        if (this.getIndex(data.callId) !== null) {
            return;
        }
        data.timeQueuePushed = Date.now();
        this.calls.push(data);
    };

    this.remove = function (callId) {
        let index = this.getIndex(callId);
        if (index !== null) {
            this.calls.splice(index, 1);
        }
    };

    this.getIndex = function (callId) {
        let index = null;
        this.calls.forEach(function (call, i) {
            if (call.callId === callId) {
                index = i;
                return false;
            }
        });
        return index;
    };

    this.pop = function () {
        return this.calls.pop();
    };

    this.getLast = function () {
        let last = this.calls[this.calls.length - 1];
        if (typeof last == 'undefined') {
            return null;
        }
        return last;
    };

    this.get = function (callId) {
        let index = this.getIndex(callId);
        if (index !== null) {
            return this.calls[index];
        }
        return null;
    };

    this.count = function () {
        return this.calls.length;
    };

    this.showAll = function () {
        this.calls.forEach(function (call) {
            console.log(call);
        })
    };
}
