const MonthInputComponent = {
    name: "cron-expression-month-component",
    template: `<div>
    <h5>Month</h5>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group" v-for="(item, index) in monthExpression">
            <input type="radio" @change="apply" v-model="selectedRadio" v-bind:name="inputRadioName" v-bind:value="index" > {{item}}
          </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex">
                <input type="radio" @change="apply" v-model="selectedRadio" v-bind:name="inputRadioName" value="0" style="margin-right: 5px;">
                <select v-bind:name="selectName" @change="applyByMonths()" v-model="selectedMonths" multiple size="10" class="form-control">
                  <option v-for="(month, index) in months"  v-bind:value="index+1">
                    {{month}}
                  </option>
                </select>
            </div>
        </div>
      </div>
    </div>`,
    props: {
        monthExpression: {
            type: Object,
        },
        inputRadioName: {
            type: String,
            default: 'monthExpression'
        },
        selectName: {
            type: String,
            default: 'selectedMonths[]'
        },
        expressionFormat: {
            type: Object,
        },
        defaultInputRadio: {
            type: [Number, null]
        }
    },
    data () {
        return {
            selectedRadio: '',
            selectedMonths: []
        };
    },
    computed: {
        months: function () {
            return ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        }
    },
    methods: {
        getFormatByValue(value) {
            return value in this.expressionFormat ? this.expressionFormat[value] : this.getFormatByMonths();
        },
        getFormatByMonths() {
            let k = 0;
            let format = '';
            let countOfSequence = 0;
            let sequenceNumbers = [];
            this.selectedMonths.forEach((e, i) => {
                if (k === 0) {
                    format += e.toString();
                    sequenceNumbers.push(e);
                } else if (k+1 !== e) {
                    format += ',' + e;
                    countOfSequence = 0;
                    sequenceNumbers = [e];
                } else if (k+1 === e && countOfSequence < 1) {
                    countOfSequence++;
                    sequenceNumbers.push(e);
                } else {
                    sequenceNumbers.push(e);
                }

                if (sequenceNumbers.length > 2) {
                    let match = /\d+(?=\D*$)/g.exec(format);
                    format = format.replace(match[0], '').slice(0, -1);
                    format += '-' + sequenceNumbers[sequenceNumbers.length - 1];
                } else if (sequenceNumbers.length === 2) {
                    format += ',' + e;
                }

                k = e;
            });

            return format;
        },
        applyByMonths() {
            this.selectedRadio = 0;
            this.apply();
        },
        apply() {
            this.$root.generate();
        },
        parse(value) {
            if (!value) {
                this.selectedRadio = '';
                this.selectedMonths = [];
                return false;
            }
            let key = Object.keys(this.expressionFormat).find(k => this.expressionFormat[k] === value);
            key in this.expressionFormat ? this.setRadioBtnByExpression(key) : this.setSelectedInputValue(value);
        },
        setRadioBtnByExpression(value) {
            this.selectedRadio = value;
        },
        setSelectedInputValue(value) {
            this.selectedMonths = [];
            let parsedExpression = value.split(',');
            parsedExpression.forEach((e, i) => {
                if (e.indexOf('-') > 0) {
                    var parsedMonths = e.split('-');
                    for (var k = parseInt(parsedMonths[0]); k <= parseInt(parsedMonths[1]); k++) {
                        this.selectedMonths.push(k);
                    }
                } else {
                    this.selectedMonths.push(e);
                }
            });
            this.selectedRadio = 0;
        }
    },
    created() {
        if (this.defaultInputRadio !== null) {
            this.selectedRadio = this.defaultInputRadio;
        }
    }
};