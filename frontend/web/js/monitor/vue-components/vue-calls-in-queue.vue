<template>
  <div class="x_panel">
    <div class="x_title">
      <h2>Calls in Queue: IVR, Queued, Hold, Delay (<span v-cloak style="color: inherit">{{ callListQueued.length }} </span>)</h2>
      <ul class="nav navbar-right panel_toolbox">
        <li>
          <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
        </li>
      </ul>
      <div class="clearfix"></div>
    </div>
    <div class="x_content">
      <transition-group name="list" tag="div" class="card-body row">
        <div v-for="(item, index) in callListQueued" class="list-item col-md-12" :key="item">
          <vue-call-item
              :item="item"
              :key="item.с_id"
              :index="index"
              :userTimeZone="userTimeZone"></vue-call-item>
        </div>
      </transition-group>
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
  name: "vue-calls-in-queue",
  components: {
    VueCallItem: Vue.defineAsyncComponent(() =>
        loadModule('/js/monitor/vue-components/vue-call-item.vue', options)
    )
  },
  props: {
    userTimeZone: String,
    filteredCallData: Array|null,
    callList: Array,
    availableCallTypeList: Array,
    availableCallSourceList: Array
  },
  computed: {
    callListQueued: function () {
      return this.getCallListByStatusId([12, 2, 1, 10], this.availableCallTypeList, this.availableCallSourceList);
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
  },
}
</script>

<style scoped>

</style>