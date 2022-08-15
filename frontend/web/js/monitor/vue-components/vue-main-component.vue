<template>
  <div class="row">
    <div class="col-md-6">
      <vue-user
          :users="usersData"
          :userTimeZone="userTimeZone"
          :filteredUserData="filteredUserData"></vue-user>
    </div>
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12">
          <vue-filters
              v-on:selectDep="selectDepartment"
              :filters="filters"
              :depList="depList"></vue-filters>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <vue-metrics
              :callList="callList"
              :userData="usersData"
              :filteredUserData="filteredUserData"
              :filteredCallData="filteredCallData"
              :availableCallTypeList="availableCallTypeList"
              :availableCallSourceList="availableCallSourceList"></vue-metrics>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <vue-calls-in-queue
          :userTimeZone="userTimeZone"
          :callList="callList"
          :filteredCallData="filteredCallData"
          :availableCallTypeList="availableCallTypeList"
          :availableCallSourceList="availableCallSourceList"></vue-calls-in-queue>
    </div>

    <div class="col-md-12">
      <vue-calls-in-progress
          :userTimeZone="userTimeZone"
          :callList="callList"
          :filteredCallData="filteredCallData"
          :availableCallTypeList="availableCallTypeList"
          :availableCallSourceList="availableCallSourceList"></vue-calls-in-progress>
    </div>

  </div>
</template>

<script>
const {loadModule} = window['vue3-sfc-loader'];

const options = {
  moduleCache: {
    vue: Vue
  },
  getFile(url) {
    return fetch(url).then((resp) =>
        resp.ok ? resp.text() : Promise.reject(resp)
    );
  },
  addStyle(styleStr) {
    const style = document.createElement("style");
    style.textContent = styleStr;
    const ref = document.head.getElementsByTagName("style")[0] || null;
    document.head.insertBefore(style, ref);
  },
  log(type, ...args) {
    console.log(type, ...args);
  }
};

export default {
  name: "vue-main-component",
  components: {
    VueUser: Vue.defineAsyncComponent(() =>
        loadModule('/js/monitor/vue-components/vue-user.vue', options)
    ),
    VueMetrics: Vue.defineAsyncComponent(() =>
        loadModule('/js/monitor/vue-components/vue-metrics.vue', options)
    ),
    VueFilters: Vue.defineAsyncComponent(() =>
        loadModule('/js/monitor/vue-components/vue-filters.vue', options)
    ),
    VueCallsInQueue: Vue.defineAsyncComponent(() =>
        loadModule('/js/monitor/vue-components/vue-calls-in-queue.vue', options)
    ),
    VueCallsInProgress: Vue.defineAsyncComponent(() =>
        loadModule('/js/monitor/vue-components/vue-calls-in-progress.vue', options)
    )
  },
  data() {
    return {
      callList: [],
      callListData: [],
      usersData: [],
      availableCallTypeList: [],
      availableCallSourceList: [],

      projectList: {},
      userList: {},
      depList: {},
      userTimeZone: 'UTC',
      userListData: [],
      depListProcessed: [],

      filteredUserData: null,
      filteredUserListData: null,
      filteredCallData: null,
      filteredCallListData: null,
      filters: {
        selectedDep: null,
      }
    };
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
    getFilteredUserData: {
      get: () => {
        if (this.filteredUserData !== null) {
          return this.filteredUserData;
        } else {
          return this.userData;
        }
      }
    }
  },
  props: {
    cfchannelname: String,
    cfuseronlinechannel: String,
    cftoken: String,
    cfconnectionurl: String,
    cfuserstatuschannel: String,
  },
  methods: {
    processingCalls(call) {
      if (this.usersData && this.usersData.length) {
        let index = this.userDataIndex(call.c_created_user_id);
        if (index !== -1) {
          call.userName = this.usersData[index].userName;
        }
      }
      if (!call.userName) {
        if (this.userList && Object.keys(this.userList).length) {
          call.userName = this.userList[call.c_created_user_id] || call.c_created_user_id;
        } else {
          call.userName = call.c_created_user_id || '';
        }
      }

      if (call.c_dep_id && this.depList && Object.keys(this.depList).length) {
        call.departmentName = this.depList[call.c_dep_id] || '-';
      } else {
        call.departmentName = '-';
      }

      if (call.c_project_id && this.projectList && Object.keys(this.projectList).length) {
        call.projectName = this.projectList[call.c_project_id] || '-';
      } else {
        call.projectName = '-';
      }

      if (call.c_source_type_id > 0 && this.callSourceList && Object.keys(this.callSourceList).length) {
        call.callSourceName = this.callSourceList[call.c_source_type_id] || '-';
      } else {
        call.callSourceName = '-';
      }

      if (call.c_call_type_id > 0 && this.callTypeList && Object.keys(this.callTypeList).length) {
        call.callTypeName = this.callTypeList[call.c_call_type_id] || '-';
      } else {
        call.callTypeName = '-';
      }

      if (call.c_status_id > 0 && this.callStatusList && Object.keys(this.callStatusList).length) {
        call.callStatusName = this.callStatusList[call.c_status_id] || '-';
      } else {
        call.callStatusName = '-';
      }
      call = this.processingCallAccess(call);

      return call;
    },
    processingCallAccess(call) {
      if (!call.userAccessList || !call.userAccessList.length) {
        call.userAccessList = [];
      }

      call.userAccessList.forEach((access) => {
        if (access.cua_status_id > 0 && this.callUserAccessStatusTypeListLabel && Object.keys(this.callUserAccessStatusTypeListLabel).length) {
          access.userAccessStatusTypeLabel = this.callUserAccessStatusTypeListLabel[access.cua_status_id];
        } else {
          access.userAccessStatusTypeLabel = 'label-default'
        }

        if (access.cua_status_id > 0 && this.callUserAccessStatusTypeList && Object.keys(this.callUserAccessStatusTypeList).length) {
          access.userAccessStatusTypeName = this.callUserAccessStatusTypeList[access.cua_status_id];
        } else {
          access.userAccessStatusTypeName = access.cua_status_id;
        }

        if (this.usersData && this.usersData.length) {
          let index = this.userDataIndex(access.cua_user_id);
          if (index !== -1) {
            access.userName = this.usersData[index].userName;
          }
        }
        if (!access.userName) {
          if (this.userList && Object.keys(this.userList).length) {
            access.userName = this.userList[access.cua_user_id] || access.cua_user_id || '';
          } else {
            access.userName = access.cua_user_id;
          }
        }
      });

      return call;
    },

    getCalls() {
      axios
          .get('/monitor/list-api')
          .then(response => {
            let callList = response.data.callList;
            if (callList && callList.length) {
              callList.forEach((call) => {
                call = this.processingCalls(call);
              });
            }

            this.callList = callList;
          })
          .catch(error => {
            console.error("There was an error!", error);
          });
    },
    staticDataApi: function () {
      axios.get("/monitor/static-data-api").then((response) => {
        console.log("RESPONSE: ", response);

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

        this.usersData = response.data.userData || [];

        this.usersData.forEach(function (item) {
          item.userDep = item.userDep.split(',').filter(Boolean).map(function(item) {
            return parseInt(item, 10);
          });
        });

        let callList = this.callList;
        if (callList && callList.length) {
          callList.forEach((call) => {
            call = this.processingCalls(call);
          });
          this.callList = callList;
        }
      });
    },

    userDataIndex(userId) {
      if (!this.userListData.length) {
        this.userListData = this.usersData.map(function (item) {
          return item.user_id && item.user_id.toString();
        });
      }
      if (userId) {
        userId = userId.toString();
      }
      return this.userListData.indexOf(userId);
    },
    deleteUserData(data) {
      let index = this.userDataIndex(data.user_id);
      if (index !== -1) {
        this.usersData.splice(index, 1);
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

      this.usersData.push(data);
      this.userListData.push(data.user_id && data.user_id.toString());
      this.addFilteredUserData(data);
    },
    updateUserData(data, updateType) {
      this.usersData = this.usersData.map((x) => {
        if (x.user_id === data.user_id) {
          x[updateType] = data[updateType];
          return x;
        }
        return x;
      });
    },
    selectDepartment(selected) {
      this.selectedDep = selected;
      this.applyFilters();
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
      callData = this.processingCalls(callData);
      this.callList = [...this.callList, callData];
      this.callListData.push(callData.c_id);
      this.addFilteredCallData(callData);
    },
    updateCall(callData) {
      this.callList = this.callList.map((x) => {
        if (x.c_id === callData.c_id) {
          callData = this.processingCalls(callData);
          return callData;
        }
        return x;
      });
      this.updateFilteredCallData(callData);
    },
    validateCall(callData) {
      let statusId = parseInt(callData.c_status_id);
      let callTypeId = parseInt(callData.c_call_type_id);
      let callSourceId = parseInt(callData.c_source_type_id);
      return this.showStatusList.includes(statusId) &&
          this.availableCallTypeList.includes(callTypeId) &&
          this.availableCallSourceList.includes(callSourceId);
    },
    callFind(callId) {
      callId = parseInt(callId);
      return this.callList.find(x => parseInt(x.c_id) === callId);
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
          this.processingCallAccess(call);
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


    isFilterEnabled() {
      return !!this.selectedDep;
    },
    resetFilters() {
      this.filteredUserData = null;
      this.filteredUserListData = null;
      this.filteredCallData = null;
      this.filteredCallListData = null;
    },
    applyFilters() {
      if (this.selectedDep) {
        this.filteredUserData = this.usersData.filter((item) => {
          return item.userDep.indexOf(this.selectedDep) !== -1;
        });
        this.filteredUserListData = this.filteredUserData.map(function (item) {
          return item.user_id && item.user_id.toString();
        });

        this.filteredCallData = this.callList.filter((item) => {
          if (!Array.isArray(item.c_dep_id)) {
            return [item.c_dep_id].indexOf(this.selectedDep) !== -1;
          } else {
            return item.c_dep_id.indexOf(this.selectedDep) !== -1;
          }
        });
        this.filteredCallListData = this.filteredCallData.map(function (item) {
          return item.c_id;
        });
      } else {
        this.resetFilters();
      }
    },
    filteredUserDataIndex(userId) {
      if (!this.filteredUserListData.length) {
        this.filteredUserListData = this.filteredUserData.map(function (item) {
          return item.user_id && item.user_id.toString();
        });
      }
      if (userId) {
        userId = userId.toString();
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
        this.filteredUserListData.push(data.user_id && data.user_id.toString());
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
    addFilteredCallData(data) {
      if (this.isFilterEnabled() && this.matchesTheFilterCriteria(data)) {
        this.filteredCallData.push(data);
        this.filteredCallListData.push(data.c_id);
      }
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
  },
  created() {
    this.staticDataApi();
    this.getCalls();
  },
  mounted() {
    let self = this;
    let centrifuge = new Centrifuge(this.cfconnectionurl, {debug: false});
    centrifuge.setToken(this.cftoken);

    centrifuge.on('connect', function(ctx){
      console.log('Connected over ' + ctx.transport);

      centrifuge.subscribe(self.cfchannelname, function(message) {
        let jsonData = message.data;
        if (jsonData.object === 'callUserAccess') {
          if (jsonData.action === 'delete') {
            self.deleteCallUserAccess(jsonData.data.callUserAccess);
          } else {
            self.addCallUserAccess(jsonData.data.callUserAccess);
          }
        } else if (jsonData.object === 'call') {
          self.actionCall(jsonData.data.call);
        }
      });

      centrifuge.subscribe(self.cfuseronlinechannel, function(message) {
        let jsonData = message.data;
        if (jsonData.object === 'userOnline') {
            if (jsonData.action === 'delete') {
              self.deleteUserData(jsonData.data.userOnline);
            } else {
                //console.info(jsonData.data);
              self.addUserData(jsonData.data.userOnline, 'online');
            }
        }
      });

      centrifuge.subscribe(self.cfuserstatuschannel, function(message) {
        let jsonData = message.data;
        // console.log(jsonData.data);
        if (jsonData.object === 'userStatus') {
          if (jsonData.action === 'delete') {
            self.deleteUserData(jsonData.data.userStatus);
          } else {
            self.addUserData(jsonData.data.userStatus, 'status');
          }
        }
      });
    });

    centrifuge.on('disconnect', function(ctx){
      console.log('Disconnected: ' + ctx.reason);
    });
    centrifuge.connect();

  },
}
</script>

<style scoped>

</style>