function initCronExpressionApp(id, curValue) {
    let cronExpression = Vue.createApp({
        components: {
            'day-input': DayInputComponent,
            'month-input': MonthInputComponent,
            'weekday-input': WeekdayInputComponent
            //'year-input': YearInputComponent,
        },
        data() {
            return {
                expression: ''
            };
        },
        computed: {
            getDayFormat() {
                return this.$refs.day.getFormatByValue(this.$refs.day.selectedRadio);
            },
            getMonthFormat() {
                return this.$refs.month.getFormatByValue(this.$refs.month.selectedRadio);
            },
            getWeekdayFormat() {
                return this.$refs.weekday.getFormatByValue(this.$refs.weekday.selectedRadio);
            }
            // getYearFormat() {
            //     return this.$refs.year.getFormatByValue(this.$refs.year.selectedRadio);
            // }
        },
        methods: {
            generate() {
                let dayFormat = this.getDayFormat;
                let monthFormat = this.getMonthFormat;
                let weekdayFormat = this.getWeekdayFormat;
                // let yearFormat = this.getYearFormat;
                this.expression = dayFormat + ' ' + monthFormat + ' ' + weekdayFormat; // + ' ' + yearFormat;
            },
            parse() {
                let expression = this.expression.replace(/  +/g, ' ').split(' ');
                this.$refs.day.parse(expression[0] || '');
                this.$refs.month.parse(expression[1] || '');
                this.$refs.weekday.parse(expression[2] || '');
                //this.$refs.year.parse(expression[3] || '');
            }
        },
        mounted() {
            if (curValue) {
                this.expression = curValue;
                this.parse();
            } else {
                this.generate();
            }
        }
    }).mount('#' + id);
}
