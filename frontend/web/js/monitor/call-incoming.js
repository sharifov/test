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
            return this.item.userName;
        },
        userIconClass() {
            return this.$root.getUserIconClass(this.item);
        },
        userTooltip() {
            return this.$root.getUserTooltipName(this.item);
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
            return !!this.item.c_is_transfer;
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
            // console.log(this.item);
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
            callListData: [],
            sortingOnline: -1,
            isAdmin: false,
            userAccessDepartments: [],
            userAccessProjects: [],
            accessCallSourceType: [],
            accessCallType: [],
            userDepartments: [],
            userProjects: [],
            showStatusList: [],

            userListData: [],
            userData: [],
            filteredUserData: null,
            filteredUserListData: null,

            filteredCallData: null,
            filteredCallListData: null,
            filters: {
                selectedDep: null,
            }
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
        selectedDep: {
            get: function () {
                return +this.filters.selectedDep;
            },
            set: function (value) {
                this.filters.selectedDep = value;
            }
        },
        callCounter: function () {
            if (this.filteredCallData !== null) {
                return this.filteredCallData.length;
            } else {
                return this.callList && this.callList.length || 0;
            }
        },
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
            return (this.filteredUserData || this.userData).length;
        },
        idleUserList: function () {
            return (this.filteredUserData || this.userData).filter(function (item) {
                if (item.online.uo_idle_state) {
                    return item;
                }
            });
        }
    },
    methods: {
        getUserName: function (userId) {
            return userId > 0 ? this.userList[userId] : userId;
        },
        getCallListByStatusId(statusList, typeList, sourceList) {
            let data;
            if (this.filteredCallData !== null) {
                data = this.filteredCallData;
            } else {
                data = this.callList;
            }

            return data.filter(function (item) {
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
        callDataIndex(callId) {
            if (!this.callListData.length) {
                this.callListData = this.callList.map(function (item) {
                    return item.c_id;
                });
            }
            return this.callListData.indexOf(callId);
        },
        actionCall(callData) {
            console.log("CALLLIST: ", this.callList);
            let index = this.callDataIndex(callData.c_id);
            if (index !== -1) {
                if (this.showStatusList.includes(callData.c_status_id)) {
                    return this.updateCall(callData);
                } else {
                    this.callList.splice(index, 1);
                    this.callListData.splice(index, 1);
                    this.deleteFilteredCallData(callData);
                    return false;
                }
            }

            if (this.validateCall(callData)) {
                this.addCall(callData);
            }
        },
        addCall(callData) {
            this.callList = [...this.callList, callData];
            this.callListData.push(callData.c_id);
            this.addFilteredCallData(callData);
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
            this.updateFilteredCallData(callData);
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
                    console.log("RESPONSE: ", response.data);
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
                    this.userTimeZone = response.data.userTimeZone;
                    this.isAdmin = response.data.isAdmin;
                    this.userAccessDepartments = response.data.userAccessDepartments;
                    this.userAccessProjects = response.data.userAccessProjects;
                    this.accessCallSourceType = response.data.accessCallSourceType;
                    this.accessCallType = response.data.accessCallType;
                    this.userDepartments = response.data.userDepartments;
                    this.userProjects = response.data.userProjects;
                    this.showStatusList = response.data.showCallStatusList;

                    this.userData = response.data.userData || [];

                    this.depListProcessed = [{
                        label: ' --- ',
                        id: null
                    }];
                    for (let dep in this.depList) {
                        if (!this.depList.hasOwnProperty(dep)) {
                            continue;
                        }
                        this.depListProcessed.push({
                            label: this.depList[dep],
                            id: dep
                        });
                    }
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

        userDataList() {
            let data;
            if (this.filteredUserData !== null) {
                data = this.filteredUserData;
            } else {
                data = this.userData;
            }

            return data
                .slice(0)
                .sort((a, b) => (a.userName && a.userName.toUpperCase() || '') < (b.userName && b.userName.toUpperCase() || '') ?
                    this.sortingOnline :
                    -this.sortingOnline
                );
        },
        updateUserData(data, updateType) {
            this.userData = this.userData.map((x) => {
                if (x.user_id === data.user_id) {
                    x[updateType] = data[updateType];
                    return x;
                }
                return x;
            });
        },
        userDataIndex(userId) {
            if (!this.userListData.length) {
                this.userListData = this.userData.map(function (item) {
                    return item.user_id;
                });
            }
            return this.userListData.indexOf(userId);
        },
        deleteUserData(data) {
            let index = this.userDataIndex(data.user_id);
            if (index !== -1) {
                this.userData.splice(index, 1);
                this.userListData.splice(index, 1);
                this.deleteFilteredUserData(data);
            }
        },
        addUserData(data, updateType) {
            let index = this.userDataIndex(data.user_id);
            if (index > -1) {
                this.updateFilteredUserData(data, updateType);
                return this.updateUserData(data, updateType);
            }

            if (!this.isAdmin && (!this.userAccessDepartments.includes(data.user_id.toString()) || !this.userAccessProjects.includes(data.user_id.toString()))) {
                return false;
            }

            this.userData.push(data);
            this.userListData.push(data.user_id);
            this.addFilteredUserData(data);
        },
        getUserIconClass(item) {
            let iconClass = 'fa fa-user text-success';
            let isUserIdle;
            if (item && item.status) {
                if ((+item.status.us_is_on_call)) {
                    iconClass = 'fa fa-phone text-success';
                } else if (!(+item.status.us_call_phone_status)) {
                    iconClass = 'fa fa-tty text-danger';
                } else if (+item.status.us_has_call_access) {
                    iconClass = 'fa fa-random';
                }
            } else {
                isUserIdle = this.idleUserList.find(x => parseInt(x.user_id) === item.user_id);
                if (isUserIdle) {
                    iconClass = 'fa fa-user text-warning';
                }
            }
            return iconClass;
        },
        getUserTooltipName(item) {
            let tooltip = 'Ready';
            let isUserIdle;
            if (item && item.status) {
                if ((+item.status.us_is_on_call)) {
                    tooltip = 'On Call';
                } else if (!(+item.status.us_call_phone_status)) {
                    tooltip = 'Busy';
                } else if (+item.status.us_has_call_access) {
                    tooltip = 'Assigned';
                }
            } else {
                isUserIdle = this.idleUserList.find(x => parseInt(x.user_id) === item.user_id);
                if (isUserIdle) {
                    tooltip = 'Idle';
                }
            }
            return tooltip;
        },

        selectDepartment(selected) {
            this.selectedDep = selected;
            this.applyFilters();
        },
        depListData() {
            return this.depListProcessed;
        },
        resetFilters() {
            this.filteredUserData = null;
            this.filteredUserListData = null;
            this.filteredCallData = null;
            this.filteredCallListData = null;
        },
        applyFilters() {
            if (this.selectedDep) {
                this.filteredUserData = this.userData.filter(function (item) {
                    return item.userDep.indexOf(this.selectedDep) !== -1;
                }.bind(this));
                this.filteredUserListData = this.filteredUserData.map(function (item) {
                    return item.user_id;
                });

                this.filteredCallData = this.callList.filter(function (item) {
                    if (!Array.isArray(item.c_dep_id)) {
                        userDepList = [item.c_dep_id];
                    } else {
                        userDepList = item.c_dep_id;
                    }
                    return userDepList.indexOf(this.selectedDep) !== -1;
                }.bind(this));
                this.filteredCallListData = this.filteredCallData.map(function (item) {
                    return item.c_id;
                });
            } else {
                this.resetFilters();
            }
        },
        isFilterEnabled() {
            return !!this.selectedDep;
        },

        filteredUserDataIndex(userId) {
            if (!this.filteredUserListData.length) {
                this.filteredUserListData = this.filteredUserData.map(function (item) {
                    return item.user_id;
                });
            }
            return this.filteredUserListData.indexOf(userId);
        },

        updateFilteredUserData(data, updateType) {
            if (this.isFilterEnabled()) {
                let index = this.filteredUserDataIndex(data.user_id);
                if (index !== -1) {
                    this.filteredUserData = this.filteredUserData.map((x) => {
                        if (x.user_id === data.user_id) {
                            x[updateType] = data[updateType];
                            return x;
                        }
                        return x;
                    });
                }
            }
        },
        addFilteredUserData(data) {
            if (this.isFilterEnabled() && this.matchesTheFilterCriteria(data)) {
                this.filteredUserData.push(data);
                this.filteredUserListData.push(data.user_id);
            }
        },
        deleteFilteredUserData(data) {
            if (this.isFilterEnabled()) {
                let index = this.filteredUserDataIndex(data.user_id);
                if (index !== -1) {
                    this.filteredUserData.splice(index, 1);
                    this.filteredUserListData.splice(index, 1);
                }
            }
        },

        filteredCallDataIndex(callId) {
            if (!this.filteredCallListData.length) {
                this.filteredCallListData = this.filteredCallData.map(function (item) {
                    return item.c_id;
                });
            }
            return this.filteredCallListData.indexOf(callId);
        },
        updateFilteredCallData(data) {
            if (this.isFilterEnabled()) {
                let index = this.filteredCallDataIndex(data.c_id);
                if (index !== -1) {
                    this.filteredCallData = this.filteredCallData.map((x) => {
                        if (x.c_id === data.c_id) {
                            return data;
                        }
                        return x;
                    });
                }
            }
        },
        addFilteredCallData(data) {
            if (this.isFilterEnabled() && this.matchesTheFilterCriteria(data)) {
                this.filteredCallData.push(data);
                this.filteredCallListData.push(data.c_id);
            }
        },
        deleteFilteredCallData(data) {
            if (this.isFilterEnabled()) {
                let index = this.filteredCallDataIndex(data.c_id);
                if (index !== -1) {
                    this.filteredCallData.splice(index, 1);
                    this.filteredCallListData.splice(index, 1);
                }
            }
        },

        matchesTheFilterCriteria(data) {
            if (this.selectedDep) {
                var userDepList;
                if (data.userDep) {
                    userDepList = data.userDep;
                } else if (data.c_dep_id) {
                    if (!Array.isArray(data.c_dep_id)) {
                        userDepList = [data.c_dep_id];
                    } else {
                        userDepList = data.c_dep_id;
                    }
                }
                if (userDepList) {
                    return data.userDep.indexOf(this.selectedDep) !== -1;
                }

                return false;
            }

            return false;
        }
    }
}).mount('#realtime-map-app');