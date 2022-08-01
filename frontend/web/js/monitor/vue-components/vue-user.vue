<template>
  <div class="x_panel">
    <div class="x_title">

      <h2>Online Users  (<span v-cloak style="color: inherit">{{ onlineUserCounter }}</span>), TimeZone: <span v-cloak style="color: inherit">{{ userTimeZone }}</span></h2>
      <ul class="nav navbar-right panel_toolbox">
        <li>
          <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
        </li>
      </ul>
      <div class="clearfix"></div>
    </div>
    <div class="x_content">
      <transition-group name="fade" tag="div" class="card-body">
        <div v-for="(item, index) in userDataList()" class="list-item truncate" :key="item" style="width: 150px;">
          <span data-toggle="tooltip" :title="userTooltip(item)"><i :class="getUserIconClass(item)"></i> {{ item.userName }}</span>
        </div>
      </transition-group>
    </div>
  </div>
</template>

<script>
export default {
  name: "user",
  data() {
    return {
      sortingOnline: -1,
    }
  },
  computed: {
    onlineUserCounter: function () {
      if (this.filteredUserData !== null) {
        return this.filteredUserData.length;
      } else {
        return this.users.length;
      }
    },
  },
  props: {
    users: Array,
    userTimeZone: String,
    filteredUserData: Array|null
  },
  methods: {
    userDataList() {
      let data;
      if (this.filteredUserData !== null) {
        data = this.filteredUserData;
      } else {
        data = this.users;
      }

      return data
          .slice(0)
          .sort((a, b) => (a.userName && a.userName.toUpperCase() || '') < (b.userName && b.userName.toUpperCase() || '') ?
              this.sortingOnline :
              -this.sortingOnline
          );
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
        // isUserIdle = this.idleUserList.find(x => parseInt(x.user_id) === item.user_id);
        if (isUserIdle) {
          iconClass = 'fa fa-user text-warning';
        }
      }
      return iconClass;
    },
    userTooltip: function (item) {
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
        // isUserIdle = this.idleUserList.find(x => parseInt(x.user_id) === item.user_id);
        if (isUserIdle) {
          tooltip = 'Idle';
        }
      }
      return tooltip;
    }
  },
  mounted() {

  }
}
</script>

<style scoped>

</style>