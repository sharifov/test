let userComponent = {
    template: '<span data-toggle="tooltip" :title="userTooltip()"><i :class="userIconClass()"></i> {{ userName() }}</span>',
    props: {
        item: Object,
        index: Number
    },
    data() {
        return {
        }
    },
    methods: {
        userName() {
            return this.$root.getUserName(this.item.uo_user_id);
        },
        userIconClass() {
            return this.$root.getUserIconClass(this.item.uo_user_id);
        },
        userTooltip() {
            return this.$root.getUserTooltipName(this.item.uo_user_id);
        }
        // stateClass() {
        //     return 'text-' + (this.item.uo_idle_state ? 'info' : 'success')
        // }
    }
}
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
            PhoneWidget.joinListen(this.callSid);
        },
        joinCoach() {
            PhoneWidget.joinCoach(this.callSid);
        },
        joinBarge() {
            PhoneWidget.joinBarge(this.callSid);
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
            userAccessList: [],
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
        if (this.$root.showStatusList.includes(this.item.c_status_id)) {
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
        showTransferLabelForCall()
        {
            return this.item.c_is_transfer ? true : false;
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
        isCallAssignedToUserGroups() {
            console.log(this.item);
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
            alert(2);
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
        getUserAccessStatusTypeLabel: function (statusTypeId) {
            return statusTypeId > 0 ? this.$root.callUserAccessStatusTypeListLabel[statusTypeId] : 'label-default';
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
        'userComponent': userComponent
    },
    data() {
        return {
            userTimeZone: 'UTC',
            projectList: [],
            depList: [],
            userList: [],
            callStatusList: [],
            callTypeList: [],
            availableCallTypeList: [],
            callSourceList: [],
            availableCallSourceList: [],
            callUserAccessStatusTypeList: [],
            callUserAccessStatusTypeListLabel: [],
            callList: [],
            onlineUserList: [],
            userStatusList: [],
            sortingOnline: -1,
            isAdmin: false,
            userAccessDepartments: [],
            userAccessProjects: [],
            accessCallSourceType: [],
            accessCallType: [],
            userDepartments: [],
            userProjects: [],
            showStatusList: [],
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
            return this.getCallListByStatusId([1], this.availableCallTypeList, this.availableCallSourceList).length;
        },
        queueCounter: function () {
            return this.getCallListByStatusId([2], this.availableCallTypeList, this.availableCallSourceList).length;
        },
        delayCounter: function () {
            return this.getCallListByStatusId([10], this.availableCallTypeList, this.availableCallSourceList).length;
        },
        inProgressCounter: function () {
            return this.getCallListByStatusId([4], this.availableCallTypeList, this.availableCallSourceList).length;
        },
        ringingCounter: function () {
            return this.getCallListByStatusId([3], this.availableCallTypeList, this.availableCallSourceList).length;
        },
        holdCounter: function () {
            return this.getCallListByStatusId([12], this.availableCallTypeList, this.availableCallSourceList).length;
        },

        callListQueued: function () {
            return this.getCallListByStatusId([12, 2, 1, 10], this.availableCallTypeList, this.availableCallSourceList);
        },
        callListInProgress: function () {
            return this.getCallListByStatusId([3, 4], this.availableCallTypeList, this.availableCallSourceList);
        },
        onlineUserCounter: function () {
            return this.onlineUserList.length;
        },
        idleUserList: function () {
            return this.onlineUserList.filter(function (item) {
                if (item.uo_idle_state) {
                    return item;
                }
            });
        }
    },
    methods: {
        getCallListByStatusId(statusList, typeList, sourceList) {
            return this.callList.filter(function (item) {
                if (item.c_status_id) {
                    let statusId = parseInt(item.c_status_id);
                    let callTypeId = parseInt(item.c_call_type_id);
                    let callSourceId = parseInt(item.c_source_type_id);
                    if (statusList.includes(statusId) && sourceList.includes(callSourceId) && typeList.includes(callTypeId)) {
                        return item;
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

            if (!this.isAdmin && (!this.userAccessDepartments.includes(data.uo_user_id.toString()) || !this.userAccessProjects.includes(data.uo_user_id.toString()))) {
                return false;
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

        userOnlineList() {
            return this.onlineUserList.slice(0).sort((a, b) => this.getUserName(a.uo_user_id).toUpperCase() < this.getUserName(b.uo_user_id).toUpperCase() ? this.sortingOnline : -this.sortingOnline)
        },
        findCallIndexById(id) {
            let index = -1;
            id = parseInt(id)
            if (this.callList) {
                index = this.callList.findIndex(item => parseInt(item.c_id) === id)
            }
            return index
        },
        removeCall(index) {
            // if (this.callList.length === 1) {
            //     this.callList = [];
            // } else {
            //     this.callList = this.callList.splice(index, 1);
            // }
            this.callList = this.callList.filter(function (elem, i) {
                return i !== index;
            });
        },
        actionCall(callData) {
            if (this.callList.find(x => parseInt(x.c_id) === parseInt(callData.c_id))) {
                if (this.showStatusList.includes(callData.c_status_id)) {
                    return this.updateCall(callData);
                } else {
                    this.removeCall(this.findCallIndexById(callData.c_id));
                    return false;
                }
            }

            if (this.validateCall(callData)) {
                this.addCall(callData);
            }
        },
        addCall(callData) {
            this.callList = [...this.callList, callData];
        },
        validateCall(callData) {
            let statusId = parseInt(callData.c_status_id);
            let callTypeId = parseInt(callData.c_call_type_id);
            let callSourceId = parseInt(callData.c_source_type_id);
            return this.showStatusList.includes(statusId) &&
                this.availableCallTypeList.includes(callTypeId) &&
                this.availableCallSourceList.includes(callSourceId);
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
                    this.availableCallTypeList = response.data.availableCallTypeList;
                    this.callSourceList = response.data.callSourceList;
                    this.availableCallSourceList = response.data.availableCallSourceList;
                    this.callUserAccessStatusTypeList = response.data.callUserAccessStatusTypeList;
                    this.callUserAccessStatusTypeListLabel = response.data.callUserAccessStatusTypeListLabel;
                    this.onlineUserList = response.data.onlineUserList;
                    this.userTimeZone = response.data.userTimeZone;
                    this.userStatusList = response.data.userStatusList;
                    this.isAdmin = response.data.isAdmin;
                    this.userAccessDepartments = response.data.userAccessDepartments;
                    this.userAccessProjects = response.data.userAccessProjects;
                    this.accessCallSourceType = response.data.accessCallSourceType;
                    this.accessCallType = response.data.accessCallType;
                    this.userDepartments = response.data.userDepartments;
                    this.userProjects = response.data.userProjects;
                    this.showStatusList = response.data.showCallStatusList;
                })
                .catch(error => {
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
                    let index = this.userAccessFindIndex(call.userAccessList, data.cua_user_id);
                    if (index > -1) {
                        call.userAccessList[index] = data;
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

        getUserIconClass(userId) {
            let iconClass = 'fa fa-user text-success'
            let item = this.userStatusFind(userId)
            let isUserIdle = this.idleUserList.find(x => parseInt(x.uo_user_id) === userId);
            if (item) {
                if ((+item.us_is_on_call)) {
                    iconClass = 'fa fa-phone text-success'
                } else if (!(+item.us_call_phone_status)) {
                    iconClass = 'fa fa-tty text-danger'
                } else if (+item.us_has_call_access) {
                    iconClass = 'fa fa-random'
                }
            } else if (isUserIdle) {
                iconClass = 'fa fa-user text-warning';
            }
            return iconClass
        },

        getUserTooltipName(userId) {
            let tooltip = 'Ready'
            let item = this.userStatusFind(userId)
            let isUserIdle = this.idleUserList.find(x => parseInt(x.uo_user_id) === userId);
            if (item) {
                if ((+item.us_is_on_call)) {
                    tooltip = 'On Call'
                } else if (!(+item.us_call_phone_status)) {
                    tooltip = 'Busy'
                } else if (+item.us_has_call_access) {
                    tooltip = 'Assigned'
                }
            } else if (isUserIdle) {
                tooltip = 'Idle';
            }
            return tooltip
        },

        userStatusFindIndex(userId) {
            let index = -1
            userId = parseInt(userId)
            if (this.userStatusList) {
                index = this.userStatusList.findIndex(x => parseInt(x.us_user_id) === userId)
            }
            return index
        },

        userStatusFind(userId) {
            userId = parseInt(userId)
            return this.userStatusList.find(x => parseInt(x.us_user_id) === userId);
        },

        deleteUserStatus(data) {
            let index = this.userStatusFindIndex(data.us_user_id)
            this.userStatusList.splice(index, 1);
        },
        addUserStatus(data) {
            let index = this.userStatusFindIndex(data.us_user_id)
            if (index > -1) {
                return this.updateUserStatus(data);
            }
            this.userStatusList = [data, ...this.userStatusList];
        },
        updateUserStatus(data) {
            this.userStatusList = this.userStatusList.map((x) => {
                if (parseInt(x.us_user_id) === parseInt(data.us_user_id)) {
                    return data;
                }
                return x;
            });
        },
    }
}).mount('#realtime-map-app');