const WeekdayInputComponent = {
    name: "cron-expression-weekday-component",
    template: `<div>
    <h5>Weekday</h5>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group" v-for="(item, index) in weekdayExpression">
            <input type="radio" @change="apply" v-model="selectedRadio" v-bind:name="inputRadioName" v-bind:value="index" > {{item}}
          </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex">
                <input type="radio" @change="apply" v-model="selectedRadio" v-bind:name="inputRadioName" value="0" style="margin-right: 5px;">
                <select v-bind:name="selectName" @change="applyByweekdays()" v-model="selectedweekdays" multiple size="10" class="form-control">
                  <option v-for="(weekday, index) in weekdays"  v-bind:value="index">
                    {{weekday}}
                  </option>
                </select>
            </div>
        </div>
      </div>
    </div>`,
    props: {
        weekdayExpression: {
            type: Object,
        },
        inputRadioName: {
            type: String,
            default: 'weekdayExpression'
        },
        selectName: {
            type: String,
            default: 'selectedweekdays[]'
        },
        expressionFormat: {
            type: Object,
        },
        defaultInputRadio: {
            type: Number,
            required: true
        }
    },
    data () {
        return {
            selectedRadio: '',
            selectedweekdays: []
        };
    },
    computed: {
        weekdays: function () {
            return ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        }
    },
    methods: {
        getFormatByValue(value) {
            return value in this.expressionFormat ? this.expressionFormat[value] : this.getFormatByweekdays();
        },
        getFormatByweekdays() {
            let k = 0;
            let format = '';
            let countOfSequence = 0;
            let sequenceNumbers = [];
            this.selectedweekdays.forEach((e, i) => {
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
        applyByweekdays() {
            this.selectedRadio = 0;
            this.apply();
        },
        apply() {
            this.$root.generate();
        },
        parse(value) {
            if (!value) {
                this.selectedRadio = '';
                this.selectedweekdays = [];
                return false;
            }
            let key = Object.keys(this.expressionFormat).find(k => this.expressionFormat[k] === value);
            key in this.expressionFormat ? this.setRadioBtnByExpression(key) : this.setSelectedInputValue(value);
        },
        setRadioBtnByExpression(value) {
            this.selectedRadio = value;
        },
        setSelectedInputValue(value) {
            this.selectedweekdays = [];
            let parsedExpression = value.split(',');
            parsedExpression.forEach((e, i) => {
                if (e.indexOf('-') > 0) {
                    var parsedWeekday = e.split('-');
                    for (var k = parseInt(parsedWeekday[0]); k <= parseInt(parsedWeekday[1]); k++) {
                        this.selectedweekdays.push(k);
                    }
                } else {
                    this.selectedweekdays.push(e);
                }
            });
            this.selectedRadio = 0;
        }
    },
    created() {
        this.selectedRadio = this.defaultInputRadio;
    }
};