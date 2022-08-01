<template>
  <div class="x_panel">
    <div class="x_title">
      <h2>Metrics</h2>
      <ul class="nav navbar-right panel_toolbox">
        <li>
          <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
        </li>
      </ul>
      <div class="clearfix"></div>
    </div>
    <div class="x_content">
      <div class="tile_count">
        <div class="col-md-4 col-sm-4  tile_stats_count">
          <span class="count_top"><i class="fa fa-list"></i> Call Items</span>
          <div class="count" v-cloak>{{ callCounter }}</div>
        </div>
        <div class="col-md-4 col-sm-4  tile_stats_count">
          <span class="count_top"><i class="fa fa-recycle"></i> IVR</span>
          <div class="count" v-cloak>{{ ivrCounter }}</div>
        </div>
        <div class="col-md-4 col-sm-4  tile_stats_count">
          <span class="count_top"><i class="fa fa-pause"></i> Queue</span>
          <div class="count" v-cloak>{{ queueCounter }}</div>
        </div>
        <div class="col-md-4 col-sm-4  tile_stats_count">
          <span class="count_top"><i class="fa fa-phone"></i> InProgress / Ringing</span>
          <div class="count" v-cloak>{{ inProgressCounter }} / {{ ringingCounter }}</div>
        </div>
        <div class="col-md-4 col-sm-4  tile_stats_count">
          <span class="count_top"><i class="fa fa-stop"></i> Delay / Hold</span>
          <div class="count" v-cloak>{{ delayCounter }} / {{ holdCounter }}</div>
        </div>
        <div class="col-md-4 col-sm-4  tile_stats_count">
          <span class="count_top"><i class="fa fa-user"></i> Idle / OnLine</span>
          <div class="count" v-cloak>{{ idleUserList.length }} / {{ onlineUserCounter }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "vue-metrics",
  props: {
    callList: Array,
    userData: Array,
    filteredUserData: Array|null,
    filteredCallData: Array|null,
    availableCallTypeList: Array,
    availableCallSourceList: Array
  },
  computed: {
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
      if (this.filteredUserData !== null) {
        return this.filteredUserData.length;
      } else {
        return this.userData.length;
      }
    },
    idleUserList: function () {
      let data;
      if (this.filteredUserData !== null) {
        data = this.filteredUserData;
      } else {
        data = this.userData;
      }

      return data.filter(function (item) {
        if (item.online.uo_idle_state) {
          return item;
        }
      });
    }
  },
  methods: {
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
  }
}
</script>

<style scoped>

</style>