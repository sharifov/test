(function () {
    function Queue() {

        this.calls = [];

        function init(data) {
            data.timeQueuePushed = Date.now();
            data.blocked = false;
            data.sentHoldUnHoldRequest = false;
            data.sentMuteUnMuteRequest = false;
            data.sentHangupRequest = false;
            data.sentReturnHoldCallRequest = false;
            data.sentAcceptCallRequest = false;
            data.sentAddNoteRequest = false;
            data.sentRejectInternalRequest = false;
        }

        this.add = function (data) {
            if (this.getIndex(data.callSid) !== null) {
                return null;
            }
            init(data);
            if (data.isHold) {
                data.holdStartTime = Date.now() - (parseInt(data.holdDuration) * 1000);
            }
            this.calls.unshift(data);
            return new window.phoneWidget.call.Call(data);
        };

        this.remove = function (callSid) {
            let index = this.getIndex(callSid);
            if (index !== null) {
                this.calls.splice(index, 1);
            }
        };

        this.getIndex = function (callSid) {
            let index = null;
            this.calls.forEach(function (call, i) {
                if (call.callSid === callSid) {
                    index = i;
                    return false;
                }
            });
            return index;
        };

        this.getLast = function () {
            let call = null;
            for (let i in this.calls) {
                if (i === 'inArray') {
                    continue;
                }
                call = this.calls[i];
            }
            if (typeof call == 'undefined' || call === null) {
                return null;
            }
            return new window.phoneWidget.call.Call(call);
        };

        this.getFirst = function () {
            let call = null;

            for (let i in this.calls) {
                if (i === 'inArray') {
                    continue;
                }
                call = this.calls[i];
                break;
            }
            if (typeof call == 'undefined' || call === null) {
                return null;
            }
            return new window.phoneWidget.call.Call(call);
        };

        this.one = function (callSid) {
            let index = this.getIndex(callSid);
            if (index !== null) {
                return new window.phoneWidget.call.Call(this.calls[index]);
            }
            return null;
        };

        this.count = function () {
            return this.calls.length;
        };

        this.all = function () {
            let calls = [];
            this.calls.forEach(function (call) {
                calls.push(new window.phoneWidget.call.Call(call));
            });
            return calls;
        };

        this.showAll = function () {
            this.calls.forEach(function (call) {
                console.log(call);
            });
        };
    }

    class QueueItem {
        constructor(queue, queueName) {
            this.queue = queue;
            this.queueName = queueName;
        };

        getList() {
            let calls = [];
            let self = this;
            this.queue.all().forEach(function (call) {
                if (call.data.queue === self.queueName) {
                    calls.push(call);
                }
            });
            return calls;
        }

        one(callSid) {
            return this.queue.one(callSid);
        }

        all() {
            let calls = this.getList();
            if (calls.length < 1) {
                return [];
            }
            let groups = [];
            let key = '';
            calls.forEach(function (call) {
                if (call.data.isInternal) {
                    if (!groups['internal']) {
                        groups['internal'] = {
                            'calls': []
                        };
                    }
                    groups['internal'].calls.push(call);
                    return;
                } else if (!call.data.project) {
                    if (!groups['external']) {
                        groups['external'] = {
                            'calls': []
                        };
                    }
                    groups['external'].calls.push(call);
                    return;
                }
                key = call.data.project + call.data.department;
                if (!groups[key]) {
                    groups[key] = {
                        project: call.data.project,
                        department: call.data.department,
                        calls: []
                    };
                }
                groups[key].calls.push(call);
            });
            return groups;
        };

        count() {
            return this.getList().length;
        }

        add(data) {
            return this.queue.add(data);
        }

        getLast() {
            return this.queue.getLast();
        }

        getFirst() {
            return this.queue.getFirst();
        }

        remove (callSid) {
            this.queue.remove(callSid);
        }
    }

    class Hold extends QueueItem {
        constructor(queue) {
            super(queue, 'hold');
        };
    }

    class Direct extends QueueItem {
        constructor(queue) {
            super(queue, 'direct');
        };
    }

    class General extends QueueItem {
        constructor(queue) {
            super(queue, 'general');
        };
    }

    function Active() {
        return new QueueItem(new Queue(), 'inProgress');
    }

    window.phoneWidget.queue = {
        Queue: Queue,
        Direct: Direct,
        Hold: Hold,
        General: General,
        Active: Active
    }
})();