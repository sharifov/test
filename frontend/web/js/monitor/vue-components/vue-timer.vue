<template>
  <span class="badge badge-warning">{{ timerIndicator }}</span>
</template>

<script>
export default {
  name: "vue-call-timer",
  props: {
    fromDt: {
      // type: String,
      required: true
    },
    tid: Number
  },
  data() {
    return {
      timeSec: 0,
      timers: null
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
      return moment.utc(inMilliseconds).format(format);
    }
  },
  methods: {
    startTimer: function () {
      this.timers = setInterval(() => {
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
  },
  beforeUnmount() {
    clearInterval(this.timers);
  }
}
</script>

<style scoped>

</style>