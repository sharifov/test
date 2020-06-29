(function () {
    function Queue() {

        this.calls = [];

        this.add = function (data) {
            if (this.getIndexByCallId(data.callId) !== null) {
                return;
            }
            data.timeQueuePushed = Date.now();
            this.calls.unshift(data);
        };

        this.remove = function (callId) {
            let index = this.getIndexByCallId(callId);
            if (index !== null) {
                this.calls.splice(index, 1);
            }
        };

        this.getIndexByCallId = function (callId) {
            let index = null;
            this.calls.forEach(function (call, i) {
                if (call.callId === callId) {
                    index = i;
                    return false;
                }
            });
            return index;
        };

        this.getIndexByCallSid = function (callSid) {
            let index = null;
            this.calls.forEach(function (call, i) {
                if (call.callSid === callSid) {
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
            return call;
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
            return call;
        };

        this.one = function (callId) {
            let index = this.getIndexByCallId(callId);
            if (index !== null) {
                return this.calls[index];
            }
            return null;
        };

        this.oneBySid = function (callSid) {
            let index = this.getIndexByCallSid(callSid);
            if (index !== null) {
                return this.calls[index];
            }
            return null;
        };

        this.count = function () {
            return this.calls.length;
        };

        this.all = function () {
            return this.calls;
        };

        this.showAll = function () {
            this.calls.forEach(function (call) {
                console.log(call);
            });
        };
    }

    class QueueItem {
        constructor(queue, state) {
            this.queue = queue;
            this.state = state;
        };

        getList() {
            let calls = [];
            let self = this;
            this.queue.all().forEach(function (call) {
                if (call.state === self.state) {
                    calls.push(call);
                }
            });
            return calls;
        }

        one(callId) {
            return this.queue.one(callId);
        }

        oneBySid(callSid) {
            return this.queue.oneBySid(callSid);
        }

        all() {
            let calls = this.getList();
            if (calls.length < 1) {
                return calls;
            }
            let groups = [];
            let key = '';
            calls.forEach(function (call) {
                if (!call.projectName) {
                    if (!groups['external']) {
                        groups['external'] = {
                            'calls': []
                        };
                    }
                    groups['external'].calls.push(call);
                    return;
                }
                key = call.projectName + call.departmentName;
                if (!groups[key]) {
                    groups[key] = {
                        projectName: call.projectName,
                        departmentName: call.departmentName,
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
            this.queue.add(data);
        }

        getLast() {
            return this.queue.getLast();
        }

        getFirst() {
            return this.queue.getFirst();
        }

        remove (callId) {
            this.queue.remove(callId);
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

    window.callWidget.queue = {
        Queue: Queue,
        Direct: Direct,
        Hold: Hold,
        General: General,
        Active: Active
    }
})();