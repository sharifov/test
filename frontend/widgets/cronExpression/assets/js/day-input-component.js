const DayInputComponent = {
    name: "cron-expression-day-component",
    template: `<div>
    <h5>Day</h5>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group" v-for="(item, index) in dayExpression">
            <input type="radio" @change="apply" v-model="selectedRadio" v-bind:name="inputRadioName" v-bind:value="index" > {{item}}
          </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex">
                <input type="radio" @change="apply" v-model="selectedRadio" v-bind:name="inputRadioName" value="0" style="margin-right: 5px;">
                <select v-bind:name="selectName" @change="applyByDays()" v-model="selectedDays" multiple size="10" class="form-control">
                  <option v-for="day in days"  v-bind:value="day">
                    {{day}}
                  </option>
                </select>
            </div>
        </div>
      </div>
    </div>`,
    props: {
        dayExpression: {
            type: Object,
        },
        inputRadioName: {
            type: String,
            default: 'dayExpression'
        },
        selectName: {
            type: String,
            default: 'selectedDays[]'
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
            selectedDays: []
        };
    },
    computed: {
        days: function () {
            let days = [];
            for (let i = 1; i <= 31; i++) {
                days.push(i);
            }
            return days;
        }
    },
    methods: {
        getFormatByValue(value) {
            return value in this.expressionFormat ? this.expressionFormat[value] : this.getFormatByDays();
        },
        getFormatByDays() {
            let k = 0;
            let format = '';
            let countOfSequence = 0;
            let sequenceNumbers = [];
            this.selectedDays.forEach((e, i) => {
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

                if (format.indexOf('-') > 0 && format.indexOf(',') > 0) {
                    console.log(format);
                    let formatParsed = format.split(',');
                    format = '';
                    formatParsed.forEach((elem,index) => {
                        if (elem.indexOf('-') > 0) {
                            let rangeSplit = elem.split('-');
                            for (let j = parseInt(rangeSplit[0]); j <= parseInt(rangeSplit[1]); j++) {
                                format += (','+j);
                            }

                            if (index == 0) {
                                format = format.slice(1);
                            }
                        } else {
                            format += index == 0 ? elem : (','+elem);
                        }
                    });
                }

                k = e;

                // if (k === 0) {
                //     format += e.toString();
                // } else {
                //     format += ',' + e;
                // }
                // k = e;
            });

            return format;
        },
        applyByDays() {
            this.selectedRadio = 0;
            this.apply();
        },
        apply() {
            this.$root.generate();
        },
        parse(value) {
            if (!value) {
                this.selectedRadio = '';
                this.selectedDays = [];
                return false;
            }
            let key = Object.keys(this.expressionFormat).find(k => this.expressionFormat[k] === value);
            key in this.expressionFormat ? this.setRadioBtnByExpression(key) : this.setSelectedInputValue(value);
        },
        setRadioBtnByExpression(value) {
            this.selectedRadio = value;
        },
        setSelectedInputValue(value) {
            this.selectedDays = [];
            let parsedExpression = value.split(',');
            console.log(parsedExpression);
            parsedExpression.forEach((e, i) => {
                if (e) {
                    if (e.indexOf('-') > 0) {
                        var parsedDays = e.split('-');
                        for (var k = parseInt(parsedDays[0]); k <= parseInt(parsedDays[1]); k++) {
                            this.selectedDays.push(k);
                        }
                    } else {
                        this.selectedDays.push(e);
                    }
                }
            });
            console.log(this.selectedDays);
            this.selectedRadio = 0;
        }
    },
    created() {
        if (this.defaultInputRadio !== null) {
            this.selectedRadio = this.defaultInputRadio;
        }
    }
};