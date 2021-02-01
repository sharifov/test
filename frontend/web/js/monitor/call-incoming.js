let Timer = {
    template: '<span class="badge badge-warning">{{ timerIndicator }}</span>',
    //props:['from'],
    props: {
        fromDt: {
            //type: String,
            required: true
        }
    },
    data() {
        return {
            timeSec: 0
        };
    },
    computed: {
        timerIndicator() {
            let duration = moment.duration(this.timeSec, 'seconds');
            let inMilliseconds = duration.asMilliseconds();

            let format = 'mm:ss';
            if (duration.days() > 0) {
                format = '[' + duration.days() + 'd], HH:mm:ss';
            } else if (duration.hours() > 0) {
                format = 'HH:mm:ss';
            }
            return moment.utc(inMilliseconds).format(format); //format("HH[h]:mm[m]:ss[s]");
        }
    },
    methods: {
        startTimer: function () {
            setInterval(() => {
                this.timeSec++;
            }, 1000);
        },
        getTimeInSeconds() {
            return this.fromDt ? Math.round((moment.utc().valueOf() - moment.utc(this.fromDt).valueOf()) / 1000) : 0;
        }
    },
    created() {
        this.timeSec = this.getTimeInSeconds();
        this.startTimer();
    }
};

const callAddUserComponent = {
    data() {
        return {
            addUserBtnHtml: '<i class="fa fa-plus"> </i> add users'
        };
    },
    template: `<button v-if="show" class="btn btn-success btn-sm" @click="showModal" v-html="addUserBtnHtml" style="margin-right: 10px;"></button>`,
    props: {
        callId: {
            type: Number,
            required: true
        },
        show: {
            type: Boolean,
            default: false
        }
    },
    methods: {
        showModal() {
            let addUserBtnHtmlDefault = this.addUserBtnHtml;
            this.addUserBtnHtml = '<i class="fa fa-spin fa-spinner"></i>';

            $.get('/call/get-users-for-call?id=' + this.callId)
                .done(function( data ) {
                        this.addUserBtnHtml = addUserBtnHtmlDefault;
                        $('#modal-md').modal('hide');

                        let modal = $('#modal-df');
                        modal.find('.modal-title').html('Add users');
                        modal.find('.modal-body').html(data);
                        modal.modal('show');
                    }.bind(this)
                );
        }
    }
};

const callJoinUserComponent = {
    template: `<div v-if="show" class="dropdown">
            <button class="btn btn-success btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-phone"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item conference-coach" href="#" @click="joinListen">Listen</a>
                <a class="dropdown-item conference-coach" href="#" @click="joinCoach">Coach</a>
                <a class="dropdown-item conference-coach" href="#" @click="joinBarge">Barge</a>
            </div>
        </div>`,
    props: {
        callSid: {
            type: String,
            // required: true
        },
        joinListenSource: {
            type: Number,
            // required: true
        },
        joinCoachSource: {
            type: Number,
            // required: true
        },
        joinBargeSource: {
            type: Number,
            // required: true
        },
        show: {
            type: Boolean,
            default: false
        }
    },
    methods: {
        joinListen() {
            joinConference('Listen', this.joinListenSource, this.callSid);
        },
        joinCoach() {
            joinConference('Coach', this.joinCoachSource, this.callSid);
        },
        joinBarge() {
            joinConference('Barge', this.joinBargeSource, this.callSid);
        }
    }
};


const callItemComponent = {
    template: '#call-item-tpl',
    components: {
        'timer': Timer,
    },
    props: {
        item: Object,
        index: Number
    },
    data() {
        return {
            show: true,
            showStatusList: [1, 2, 3, 4, 10, 12],
            userAccessList2: []
        };
    },
    created() {
        // this.show = true;
        /*if (this.showStatusList.includes(this.item.c_status_id)) {
            this.show = true;
        } else {
            this.show = false;
        }*/
    },
    updated() {
        if (this.showStatusList.includes(this.item.c_status_id)) {
            this.show = true;
        } else {
            //this.show = false;
            this.removeElement(this.item.c_id);
        }
    },
    computed: {
        projectName() {
            return this.item.c_project_id > 0 ? this.$root.projectList[this.item.c_project_id] : '-';
        },
        departmentName() {
            return this.item.c_dep_id ? this.$root.depList[this.item.c_dep_id] : '-';
        },
        callSourceName() {
            return this.item.c_source_type_id > 0 ? this.$root.callSourceList[this.item.c_source_type_id] : '-';
        },
        callStatusName() {
            return this.item.c_status_id > 0 ? this.$root.callStatusList[this.item.c_status_id] : '-';
        },
        callTypeName() {
            return this.item.c_call_type_id > 0 ? this.$root.callTypeList[this.item.c_call_type_id] : '-';
        },
        clientFullName() {
            let name = '';
            if (this.item.client) {
                name += this.item.client.first_name ? this.item.client.first_name : '';
                name += this.item.client.last_name ? this.item.client.last_name : '';
                //'middle_name'
                name = name.trim();
                if (name === 'ClientName') {
                    name = '- noname -';
                }
            }
            return name;
        },

        callStatusTimerDateTime() {
            let dt = this.item.c_created_dt;
            if (this.item.c_updated_dt) {
                dt = this.item.c_updated_dt;
            }
            if (parseInt(this.item.c_status_id) === 2 && this.item.c_queue_start_dt) {
                dt = this.item.c_queue_start_dt;
            }
            // console.log(dt);
            return dt;
        },
    },

    // data() {
    //      return {
    //             projectList: this.projectList
    //     }
    // },
    methods: {
        removeElement(index) {
            this.$root.removeCall(index); //callList.splice(index, 1);
            //this.$delete(this.finds, index)
        },

        //check() { this.checked = !this.checked; },
        getUserName: function (userId) {
            return this.$root.getUserName(userId);
        },
        getUserAccessStatusTypeName: function (statusTypeId) {
            return statusTypeId > 0 ? this.$root.callUserAccessStatusTypeList[statusTypeId] : statusTypeId;
        },
        createdDateTime(format) {
            let val = '';
            if (this.item.c_created_dt) {
                let obj = moment.utc(this.item.c_created_dt);
                if (this.$root.userTimeZone) {
                    val = obj.tz(this.$root.userTimeZone).format(format);
                } else {
                    val = obj.format(format);
                }
            }
            return val;
        },

        formatPhoneNumber(phoneNumber) {
            return new libphonenumber.AsYouType().input(phoneNumber); //phoneUtil.format(phoneNumber, PNF.INTERNATIONAL);
        },
        getCountryByPhoneNumber(phoneNumber) {
            if (phoneNumber) {
                try {
                    let phoneData = new libphonenumber.parsePhoneNumberFromString(phoneNumber);
                    if (phoneData) {
                        return phoneData.country;
                    }
                } catch (error) {
                    console.error(error.message);
                }
            }
            return '';
        },

        showModalWindow() {
            let modal = $('#modal-md');
            modal.find('.modal-title').html('Call Info');
            modal.find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
            modal.modal('show');

            axios
                .get('/call/get-call-info?sid=' + this.item.c_call_sid)
                .then(response => {
                    modal.find('.modal-body').html(response.data);
                })
                .catch(error => {
                    createNotify('Error', error, 'error');
                    setTimeout( () => {
                        modal.modal('hide');
                    }, 300);
                });
        }

        // "callStatusList": {
        //     "1": "IVR",
        //     "2": "Queued",
        //     "3": "Ringing",
        //     "4": "In progress",
        //     "5": "Completed",
        //     "6": "Busy",
        //     "7": "No answer",
        //     "8": "Failed",
        //     "9": "Canceled",
        //     "10": "Delay",
        //     "11": "Declined",
        //     "12": "Hold"
        // },

        // "callTypeList": {
        //     "1": "Outgoing",
        //     "2": "Incoming",
        //     "3": "Join",
        //     "4": "Return"
        // },

    }
};

var callMapApp = Vue.createApp({
    components: {
        'callItemComponent': callItemComponent,
    },
    data() {
        return {
            userTimeZone: 'UTC',
            projectList: [],
            depList: [],
            userList: [],
            callStatusList: [],
            callTypeList: [],
            callSourceList: [],
            callUserAccessStatusTypeList: [],
            callList: [],
            onlineUserList: []
        };
    },
    created() {
        setInterval(() => {
            this.getStaticData();
        }, 60 * 60 * 1000); // 1 hour
        this.getStaticData();
        this.getCalls();
    },
    computed: {
        ivrCounter: function () {
            return this.getCallListByStatusId([1]).length;
        },
        queueCounter: function () {
            return this.getCallListByStatusId([2]).length;
        },
        delayCounter: function () {
            return this.getCallListByStatusId([10]).length;
        },
        inProgressCounter: function () {
            return this.getCallListByStatusId([4]).length;
        },
        ringingCounter: function () {
            return this.getCallListByStatusId([3]).length;
        },
        holdCounter: function () {
            return this.getCallListByStatusId([12]).length;
        },

        callListQueued: function () {
            return this.getCallListByStatusId([12, 2, 1, 10]);
        },
        callListInProgress: function () {
            return this.getCallListByStatusId([3, 4]);
        },
        onlineUserCounter: function () {
            return this.onlineUserList.length;
        },
        idleUserList: function () {
            return this.onlineUserList.filter(function (item) {
                if (!item.uo_idle_state) {
                    return item;
                }
            });
        }
    },
    methods: {
        getCallListByStatusId(statusList, typeList) {
            return this.callList.filter(function (item) {
                if (item.c_status_id) {
                    let statusId = parseInt(item.c_status_id);
                    let callTypeId = parseInt(item.c_call_type_id);
                    if (statusList.includes(statusId)) {
                        if (typeList) {
                            if (typeList.includes(callTypeId)) {
                                return item;
                            }
                        } else {
                            return item;
                        }
                    }
                }
            });
        },

        userOnlineFindIndex(userId) {
            let index = -1;
            userId = parseInt(userId);
            if (this.onlineUserList) {
                index = this.onlineUserList.findIndex(x => parseInt(x.uo_user_id) === userId);
            }
            return index;
        },
        deleteUserOnline(data) {
            let index = this.userOnlineFindIndex(data.uo_user_id);
            this.onlineUserList.splice(index, 1);
        },
        addUserOnline(data) {
            let index = this.userOnlineFindIndex(data.uo_user_id);
            if (index > -1) {
                return this.updateUserOnline(data);
            }
            this.onlineUserList = [data, ...this.onlineUserList];
            //this.callList.push(callData);
        },

        updateUserOnline(data) {
            this.onlineUserList = this.onlineUserList.map((x) => {
                if (x.uo_user_id === data.uo_user_id) {
                    return data;
                }
                return x;
            });
        },

        removeCall(index) {
            console.log(index);
            this.callList = this.callList.splice(index, 1);
        },
        addCall(callData) {
            if (this.callList.find(x => x.c_id === callData.c_id)) {
                return this.updateCall(callData);
            } else {
                this.callList = [callData, ...this.callList];
            }
            //this.callList.push(callData);
        },
        updateCall(callData) {
            this.callList = this.callList.map((x) => {
                if (x.c_id === callData.c_id) {
                    return callData;
                }
                return x;
            });
        },
        getCalls() {
            axios
                .get('/monitor/list-api')
                .then(response => {
                    this.callList = response.data.callList;
                })
                .catch(error => {
                    console.error("There was an error!", error);
                });
        },
        getStaticData() {
            axios
                .get('/monitor/static-data-api')
                .then(response => {
                    this.projectList = response.data.projectList;
                    this.depList = response.data.depList;
                    this.userList = response.data.userList;
                    this.callStatusList = response.data.callStatusList;
                    this.callTypeList = response.data.callTypeList;
                    this.callSourceList = response.data.callSourceList;
                    this.callUserAccessStatusTypeList = response.data.callUserAccessStatusTypeList;
                    this.onlineUserList = response.data.onlineUserList;
                    this.userTimeZone = response.data.userTimeZone;
                })
                .catch(error => {
                    console.log(this.callUserAccessStatusTypeList);
                    console.error("There was an error!", error);
                });
        },

        callFind(callId) {
            callId = parseInt(callId);
            return this.callList.find(x => parseInt(x.c_id) === callId);
        },

        userAccessFind(objectList, userId) {
            let item = [];
            if (objectList) {
                userId = parseInt(userId);
                item = objectList.find(x => parseInt(x.cua_user_id) === userId);
            }
            return item;
        },

        userAccessFindIndex(objectList, userId) {
            let index = -1;
            userId = parseInt(userId);
            if (objectList) {
                index = objectList.findIndex(x => parseInt(x.cua_user_id) === userId);
            }
            return index;
        },

        addCallUserAccess(data) {
            if (data && data.cua_call_id) {
                let call = this.callFind(data.cua_call_id);
                if (call) {
                    //let userAccess = this.userAccessFind(call.userAccessList, data.cua_user_id);
                    //console.log(data);
                    //console.log(userAccess);
                    //console.log(call.userAccessList);

                    //if (userAccess) {
                    //console.info(call.userAccessList[0]);
                    let index = this.userAccessFindIndex(call.userAccessList, data.cua_user_id);
                    if (index > -1) {
                        call.userAccessList[index] = data;
                        //  }
                    } else {
                        call.userAccessList = [data, ...call.userAccessList];
                    }
                }
            }
        },

        deleteCallUserAccess(data) {
            if (data && data.cua_call_id) {
                let call = this.callFind(data.cua_call_id);
                if (call) {
                    let index = this.userAccessFindIndex(call.userAccessList, data.cua_user_id);
                    if (index > -1) {
                        call.userAccessList.splice(index, 1);
                    }
                }
            }
        },
        getUserName: function (userId) {
            return userId > 0 ? this.userList[userId] : userId;
        },
    }
}).mount('#realtime-map-app');