<template>
  <div v-if="show" class="col-md-12" style="margin-bottom: 0px">
    <table class="table table-condensed  table-bordered">
      <tbody>
      <tr>
        <td class="text-center" style="width:35px">
          {{ index + 1 }}
          <br>
          <div class="call-menu" @click="showModalWindow"><i class="fas fa-ellipsis-v"></i></div>
        </td>
        <td class="text-center" style="width:80px">
          <u><a :href="'/call/view?id=' + item.c_id" target="_blank">{{ item.c_id }}</a></u><br>
          <b>{{ item.callTypeName }}</b>
        </td>
        <td class="text-center" style="width:90px">
          <i class="fa fa-clock-o"></i> {{ createdDateTime("HH:mm") }}<br>
          <span v-if="item.c_source_type_id">{{ item.callSourceName }}</span>
        </td>

        <td class="text-center" style="width:140px">
          <span class="badge badge-info">{{ item.projectName }}</span><br>
          <span v-if="item.c_dep_id" class="label label-default">{{ item.departmentName }}</span>
        </td>
        <td class="text-left" style="width:70px">
          <img v-if="getCountryByPhoneNumber(item.c_from)" :src="'https://flagcdn.com/20x15/' + getCountryByPhoneNumber(item.c_from).toLowerCase() + '.png'" width="20" height="15" :alt="getCountryByPhoneNumber(item.c_from)"/>
          {{ getCountryByPhoneNumber(item.c_from) }}
        </td>

        <td class="text-left" style="width:180px">
          <div v-if="item.c_client_id" class="crop-line">
            <i class="fa fa-male text-info fa-1x fa-border"></i>&nbsp;
            <span v-if="item.client">
              <a :href="'/client/view?id=' + item.c_client_id" target="_blank">
                <small style="text-transform: uppercase">{{ clientFullName }}</small>
              </a>
            </span>
          </div>
          <i class="fa fa-phone fa-1x fa-border"></i> {{ formatPhoneNumber(item.c_from) }}
        </td>

        <td class="text-center" style="width:120px">
          <b>{{ item.callStatusName }}</b>
          <br>
          <label class="label label-warning" v-show="showTransferLabelForCall">Transfer</label>
          {{ isCallAssignedToUserGroups }}
        </td>
        <td class="text-center" style="width:120px">
            <vue-timer :fromDt="callStatusTimerDateTime" :tid="item.c_id"></vue-timer>
        </td>

        <td class="text-left" style="width:160px">
          <div v-if="item.c_created_user_id">
            <i class="fa fa-user fa-1x fa-border text-success"></i>
            {{ item.userName }}<br>
            <i class="fa fa-phone fa-1x fa-border"></i>
            <small>{{ formatPhoneNumber(item.c_to) }}</small>
          </div>
          <div v-else>
            <i class="fa fa-phone fa-1x fa-border"></i>
            {{ formatPhoneNumber(item.c_to) }}
          </div>
        </td>
      </tr>
      </tbody>
    </table>
    <div v-if="item.userAccessList && item.userAccessList.length > 0" class="text-right" style="margin-bottom: 5px">
      <transition-group name="fade">
        <span class="label" :class="access.userAccessStatusTypeLabel"
              v-for="(access, index) in item.userAccessList" :key="access.cua_user_id"
              style="margin-right: 4px" :title="access.userAccessStatusTypeName">
            <i class="fa fa-user"></i> {{ access.userName }}
        </span>
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
  name: "vue-call-item",
  components: {
    VueTimer: Vue.defineAsyncComponent(() =>
        loadModule('/js/monitor/vue-components/vue-timer.vue', options)
    )
  },
  props: {
    item: Object,
    index: Number,
    userTimeZone: String
  },
  data() {
    return {
      show: true,
    };
  },
  computed: {
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
  methods: {
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
    formatPhoneNumber(phoneNumber) {
      return new libphonenumber.AsYouType().input(phoneNumber); //phoneUtil.format(phoneNumber, PNF.INTERNATIONAL);
    },
    createdDateTime(format) {
      let val = '';
      if (this.item.c_created_dt) {
        let obj = moment.utc(this.item.c_created_dt);
        if (this.userTimeZone) {
          val = obj.tz(this.userTimeZone).format(format);
        } else {
          val = obj.format(format);
        }
      }
      return val;
    },
    showTransferLabelForCall() {
      return !!this.item.c_is_transfer;
    },
  }
}

</script>

<style scoped>

</style>