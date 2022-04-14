( function (window) {
    var ChatApp = window.ChatApp || {};
    var Promise = window.Promise;

    function DataStore()
    {
        this.initData = function () {
            this.data = [];

            this.data.keySort = function (keys) {
                keys = keys || {};

                var obLen = function(obj) {
                    var size = 0, key;
                    for (key in obj) {
                        if (obj.hasOwnProperty(key))
                            size++;
                    }
                    return size;
                };

                var obIx = function(obj, ix) {
                    var size = 0, key;
                    for (key in obj) {
                        if (obj.hasOwnProperty(key)) {
                            if (size == ix)
                                return key;
                            size++;
                        }
                    }
                    return false;
                };

                var keySort = function(a, b, d) {
                    d = d !== null ? d : 1;
                    if (a == b)
                        return 0;
                    return a > b ? 1 * d : -1 * d;
                };

                var KL = obLen(keys);

                if (!KL)
                    return this.sort(keySort);

                for ( var k in keys) {
                    keys[k] =
                        keys[k] == 'desc' || keys[k] == -1  ? -1
                            : (keys[k] == 'skip' || keys[k] === 0 ? 0
                            : 1);
                }

                this.sort(function(a, b) {
                    var sorted = 0, ix = 0;

                    while (sorted === 0 && ix < KL) {
                        var k = obIx(keys, ix);
                        if (k) {
                            var dir = keys[k];
                            sorted = keySort(a[k], b[k], dir);
                            ix++;
                        }
                    }
                    return sorted;
                });
                return this;
            }
        }.bind(this);

        this.existInData = function (prop, val)
        {
            if (this.data.length > 0 ) {
                for (i in this.data) {
                    if (this.data[i][prop] === val) {
                        return true;
                    }
                }
            }
            return false;
        }.bind(this);

        this.getKeyByProperty = function (prop, val) {
            if (!val) {
                return undefined;
            }

            if (this.data.length > 0 ) {
                for (i in this.data) {
                    if (this.data[i][prop] == val) {
                        return i;
                    }
                }
            }
            return undefined;
        }

        this.defaultSort = {
            'ccc_id': 'desc',
            'cch_created_dt': 'asc'
        }

        this.initData();
    }

    DataStore.prototype.add = function (item) {
        var promise = new Promise(function(resolve, reject) {
            if (!this.existInData('ccua_id', item.ccua_id)) {
                this.data.push(item);
            }
            resolve();
        }.bind(this));
        return promise;
    }

    DataStore.prototype.addBatch = function (data) {
        var promise = new Promise(function(resolve, reject) {
            if (data) {
                data.forEach( function (item) {
                    if (!this.existInData('ccua_id', item.ccua_id)) {
                        this.data.push(item);
                    }
                }.bind(this));
            }
            resolve();
        }.bind(this));
        return promise;
    }

    DataStore.prototype.get = function(key) {
        var promise = new Promise(function (resolve, reject) {
            resolve(this.data[key]);
        }.bind(this));

        return promise;
    }

    DataStore.prototype.getAll = function () {
        return this.data;
    }

    DataStore.prototype.sortData = function () {
        var promise = new Promise(function(resolve, reject) {
            this.data.keySort(this.defaultSort);
            resolve();
        }.bind(this));

        return promise;
    }

    DataStore.prototype.remove = function(key) {
        var promise = new Promise(function(resolve, reject) {
            delete this.data[key];
            resolve();
        }.bind(this));
        return promise;
    };

    DataStore.prototype.removeAll = function() {
        var promise = new Promise(function(resolve, reject) {
            this.initData();
            resolve();
        }.bind(this));
        return promise;
    };

    DataStore.prototype.deleteByRequestId = function(id) {
        var promise = new Promise(function(resolve, reject) {
            let key = this.getKeyByProperty('ccua_id', id);
            if (key >= 0) {
                this.data.splice(key, 1);
                resolve();
            }
        }.bind(this));
        return promise;
    };

    ChatApp.DataStore = DataStore;
    window.ChatApp = ChatApp;
})(window);