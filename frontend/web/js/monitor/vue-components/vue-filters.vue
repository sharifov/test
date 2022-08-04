<template>
  <div class="x_panel">
    <div class="x_title">
      <h2>Filters: </h2>
      <ul class="nav navbar-right panel_toolbox">
        <li>
          <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
        </li>
      </ul>
      <div class="clearfix"></div>
    </div>
    <div class="x_content">
      <div class="col-md-4">
        <div class="form-group field-leadsearch-employee_id">
          <label class="control-label" for="leadsearch-employee_id">Select department:</label>
          <select v-model="filters.selectedDep" class="form-control" v-on:input="selectDepartment($event.target.value)">
            <option v-for="option in depListProcessed" :value="option.id">
              {{ option.label }}
            </option>
          </select>
          <div class="help-block"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "vue-filters",
  data() {
    return {
      depListProcessed: []
    }
  },
  props: {
    filters: Object,
    depList: Object
  },
  watch: {
    depList: function (newVal, oldVal) {
      let list = [{
        label: ' --- ',
        id: null
      }];
      for (let dep in newVal) {
        if (!newVal.hasOwnProperty(dep)) {
          continue;
        }
        list.push({
          label: newVal[dep],
          id: dep
        });
      }
      this.depListProcessed = Object.assign({}, list);
    }
  },
  methods: {
    selectDepartment(selected) {
      this.$emit('selectDep', selected);
    }
  }
}
</script>

<style scoped>

</style>