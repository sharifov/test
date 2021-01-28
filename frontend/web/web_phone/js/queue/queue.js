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
            data.sentRecordingRequest = false;
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
            if (index === null) {
                return false;
            }
            this.calls.splice(index, 1);
            return true;
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
            if (this.count() === 0) {
                return null;
            }

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
            if (this.count() === 0) {
                return null;
            }

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

        this.reset = function () {
            this.calls = [];
        }
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

        reset () {
            this.queue.reset();
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

    function PriorityItem(project, department, count) {
        return {
            project: project,
            department: department,
            count: count
        };
    }

    function Priority() {
        this.items = [];
        this.accepted = false;

        this.reset = function () {
            this.items = [];
        };

        this.remove = function (project, department) {
            let item = this.findByCondition(project, department);
            if (item.count === 0) {
                if (item.index === null) {
                    return;
                }
                this.items.splice(item.index, 1);
                return;
            }
            this.items[item.index].count--;
            if (this.items[item.index].count < 1) {
                this.items.splice(item.index, 1);
            }
        };

        this.add = function (project, department) {
            this.addMany(project, department, 1);
        };

        this.addMany = function (project, department, count) {
            let item = this.findByCondition(project, department);
            if (item.count === 0) {
                this.items.push(new PriorityItem(project, department, count));
                return;
            }
            this.items[item.index].count += count;
        };

        this.findByCondition = function (project, department) {
            let index = null;
            this.items.forEach(function (item, i) {
                if (item.project === project && item.department === department) {
                    index = i;
                }
            });
            if (index === null) {
                return {
                    'count': 0,
                    'index': null
                };
            }
            return {
                'count': this.items[index].count,
                'index': index
            };
        };

        this.count = function () {
            let count = 0;
            this.items.forEach(function (item) {
                count += item.count;
            });
            return count;
        };

        this.accept = function () {
            this.accepted = true;
        };

        this.unAccept = function () {
            this.accepted = false;
        };

        this.isAccepted = function () {
            return this.accepted === true;
        };
    }

    window.phoneWidget.queue = {
        Queue: Queue,
        Direct: Direct,
        Hold: Hold,
        General: General,
        Active: Active,
        Priority: Priority
    }
})();