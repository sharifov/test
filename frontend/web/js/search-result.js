/*jshint multistr: true */
/* jshint strict: false */
/* globals moment */
/* globals JSON */
/* globals noUiSlider */
/* globals console */

SearchResult = function(props) {
    if (typeof props === "undefined") props = {};
    var scope = this,
        checkboxIndex = 0,
        originalData = null,
        filteredData = null,
        filterList = {},
        locale = $.extend(true, {
            time: "Time",
            departText: "DEPART",
            returnText: "RETURN",
            flight: "Flight",
            takeoff: "Takeoff",
            anyTime: "Any Time",
            landing: "Landing",
            airlines: "Airlines",
            allAirlines: "All Airlines",
            only: "Only",
            of: "of",
            flights: "flights",
            noFlightsFound: "No flights have been found",
            stop: "STOP",
            nonstop: "NONSTOP",
            seatsLeft: "seats left",
            operatedBy: "Operated by",
            viewDetails: "View Details",
            bookFor: "Book For",
            layoverIn: "Layover in",
            shortLayover: "Short Layover",
            departLowerText: "Depart",
            returnLowerText: "Return",
            timeTexts: {
                hour: 'h',
                minute: 'm'
            },
            filterGeneral: {
                under: "Under"
            },
            filterPrice: {
                price: "Price",
                to: "To"
            },
            filterStops: {
                stops: "Stops"
            },
            filterDuration: {
                duration: "Duration"
            },
            currency: {
                name: "USD",
                symbol: "$"
            }
        }, props.locale || {}),
        currentTripType = props.currentTripType || 'rt';

    this.init = function() {
        // init filters
        scope.filterInit();

        // init listeners
        $('.js-filter .dropdown-menu').on('click', function(e) {
            if (!$(e.target).hasClass("js-dropdown-close")) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    };

    this.addFilterParams = function(params) {
        if (params.name === 'leg') {
            if (!filterList[params.name])
                filterList[params.name] = [];

            var temp = filterList[params.name].filter(function(obj) {
                return obj.data !== params.value.data;
            });
            if (temp.length === filterList[params.name].length) {
                filterList[params.name].push(params.value);
            } else {
                filterList[params.name] = temp;
            }
        } else if (params.name === 'travelTime') {
            if (!filterList[params.name])
                filterList[params.name] = {};
            if (!filterList[params.name][params.index]) {
                filterList[params.name][params.index] = {};
            }

            filterList[params.name][params.index][params.direction] = params.value;
        } else {
            filterList[params.name] = params.value;
        }
        window.filterList = filterList;
        scope.filterApply();
    };

    this.unsetFilterParams = function(name) {
        if (filterList[name]) {
            delete filterList[name];
            scope.filterApply();
        }
    };

    this.filterApply = function() {
        if (Object.keys(filterList).length) {
            var filterApplied = false;
            $('.search-result__quote').removeClass('filtered');
            for (var filter in filterList) {
                if (!filterList.hasOwnProperty(filter)) continue;
                var selector = '.search-result__quote';
                if(filterApplied === true){
                    selector = '.search-result__quote.filtered';
                }

                $(selector).addClass('hide');
                let cnt = 0;
                switch (filter) {
                    case 'price':
                        $(selector).each(function(idx){
                            if(+$(this)[0].getAttribute('data-price') <= filterList[filter] * 1){
                                $(this).removeClass('hide');
                                $(this).addClass('filtered');
                                filterApplied = true;
                            }
                        });
                        break;
                    case 'stops':
                        var stops = 0;
                        switch (filterList[filter]) {
                            case 'stop-1':
                                stops = 1;
                                break;
                            case 'stop-2':
                                stops = 2;
                                break;
                            case 'nonstop':
                                stops = 0;
                                break;
                            default:
                                stops = 1;
                                break;
                        }
                        $(selector).each(function(idx){
                            var stopsData = $(this).data('stop');
                            var obj = $(this);
                            cnt = 0;
                            stopsData.forEach(function(stop){
                                if(stop <= stops){
                                    cnt++;
                                }
                            });

                            if(stopsData.length == cnt){
                                $(obj).removeClass('hide');
                                $(obj).addClass('filtered');
                                filterApplied = true;
                            }
                        });
                        break;
                    case 'airlineChange':
                        cnt = 0;
                        $(selector).each(function(idx){
                            let hasAirportchange = $(this).data('changeairport');
                            let obj = $(selector+'[data-changeairport="'+hasAirportchange+'"]');
                            if (hasAirportchange != 1){
                                cnt++;
                                $(obj).removeClass('hide');
                                $(obj).addClass('filtered');
                                filterApplied = true;
                            }
                        });
                        break;
                    case 'baggage' :
                        let baggage = 0;
                        switch (filterList[filter]) {
                            case 'pcs1':
                                baggage = 1;
                                cnt = 0;
                                $(selector).each(function(idx){
                                    cnt++;
                                    let obj = $(selector+'[data-baggage="1"]');
                                    $(obj).removeClass('hide');
                                    $(obj).addClass('filtered');
                                    let obj2 = $(selector+'[data-baggage="2"]');
                                    $(obj2).removeClass('hide');
                                    $(obj2).addClass('filtered');
                                    filterApplied = true;
                                });
                                break;
                            case 'pcs2':
                                baggage = 2;
                                cnt = 0;
                                $(selector).each(function(idx){
                                    let obj = $(selector+'[data-baggage="'+baggage+'"]');
                                    cnt++;
                                    $(obj).removeClass('hide');
                                    $(obj).addClass('filtered');
                                    filterApplied = true;
                                });
                                break;
                        }
                        break;
                    case 'fareType':
                        filterList[filter].forEach(function(fareType) {
                            var obj = $(selector+'[data-fareType="'+fareType+'"]');
                            $(obj).removeClass('hide');
                            $(obj).addClass('filtered');
                            filterApplied = true;
                        });
                        break;
                    case 'airline':
                        filterList[filter].forEach(function(airline) {
                            var obj = $(selector+'[data-airline="'+airline+'"]');
                            $(obj).removeClass('hide');
                            $(obj).addClass('filtered');
                            filterApplied = true;
                        });
                        break;
                    case 'travelTime':
                        $(selector).each(function(idx){
                            var obj = $(this);
                            var time = $(this).data('time');
                            var cnt = 0;
                            var cntTrue = 0;

                            for(var i in filterList[filter]){
                                if(!filterList[filter].hasOwnProperty(i)) continue;
                                cnt++;

                                if (filterList[filter][i].depart && filterList[filter][i].arrival) {
                                    if(scope.helper.dateBetweenToTimes(filterList[filter][i].depart, time[i].departure) &&
                                        scope.helper.dateBetweenToTimes(filterList[filter][i].arrival, time[i].arrival)){
                                        cntTrue++;
                                    }
                                }else{
                                    if(filterList[filter][i].depart && scope.helper.dateBetweenToTimes(filterList[filter][i].depart, time[i].departure) ||
                                        filterList[filter][i].arrival && scope.helper.dateBetweenToTimes(filterList[filter][i].arrival, time[i].arrival)){
                                        cntTrue++;
                                    }
                                }
                            }
                            if(cnt == cntTrue){
                                $(obj).removeClass('hide');
                                $(obj).addClass('filtered');
                                filterApplied = true;
                                return;
                            }
                        });
                        break;
                    case 'duration':
                        $(selector).each(function(idx){
                            var obj = $(this);
                            /*var duration = $(this).data('totalduration');
                            if(duration <= filterList[filter]){
                                 $(obj).removeClass('hide');
                                 $(obj).addClass('filtered');
                                 filterApplied = true;
                             }*/
                            var durations = $(this).data('duration');
                            var cnt = 0;

                            durations.forEach(function(duration){
                                if(duration <= filterList[filter]){
                                    cnt++;

                                }
                            });

                            if(durations.length == cnt){
                                $(obj).removeClass('hide');
                                $(obj).addClass('filtered');
                                filterApplied = true;
                                return;
                            }

                        });
                        break;
                }
            }
        }else{
            $('.search-result__quote').removeClass('hide');
        }
        $('#search-results__cnt').html($('.search-result__quote:not(.hide)').length);
    };

    this.filterStops = function() {
        $(".filter--connections .custom-radio").on("click", function() {
            var radio = $(this).find('input[type="radio"]');
            radio.prop('checked', true);
            if (radio.attr("id") !== "any") {
                $('.filter--connections').addClass("selected")
                    .find('[data-toggle="dropdown"] span').html(radio.parent().find('label:last').html());
                scope.addFilterParams({
                    name: 'stops',
                    value: radio.attr("id")
                });
            } else {
                $('.filter--connections').removeClass("selected")
                    .find('[data-toggle="dropdown"] span').html(locale.filterStops.stops);
                scope.unsetFilterParams('stops');
            }
        });

        $(".filter--connections .js-clear-filter").on("click", function(e) {
            e.stopImmediatePropagation();
            $('.filter--connections').removeClass("selected")
                .find('[data-toggle="dropdown"] span').html(locale.filterStops.stops);
            $(".filter--connections .custom-radio").find('input[type="radio"]:first').prop('checked', true);
            scope.unsetFilterParams("stops");
        });
    };

    this.filterAirlineChange = function() {
        $(".filter--airportChange .custom-radio").on("click", function() {
            var radio = $(this).find('input[type="radio"]');
            radio.prop('checked', true);
            if (radio.attr("id") !== "anyAirport") {
                $('.filter--airportChange').addClass("selected")
                    .find('[data-toggle="dropdown"] span').html(radio.parent().find('label:last').html());
                scope.addFilterParams({
                    name: 'airlineChange',
                    value: radio.attr("id")
                });
            } else {
                $('.filter--airportChange').removeClass("selected")
                    .find('[data-toggle="dropdown"] span').html(/*locale.filterStops.stops*/);
                scope.unsetFilterParams('airlineChange');
            }
        });

        $(".filter--airportChange .js-clear-filter").on("click", function(e) {
            e.stopImmediatePropagation();
            $('.filter--airportChange').removeClass("selected")
                .find('[data-toggle="dropdown"] span').html(/*locale.filterStops.stops*/);
            $(".filter--airportChange .custom-radio").find('input[type="radio"]:first').prop('checked', true);
            scope.unsetFilterParams("airlineChange");
        });
    };

    this.filterBaggage = function() {
        $(".filter--baggage .custom-radio").on("click", function() {
            var radio = $(this).find('input[type="radio"]');
            radio.prop('checked', true);
            if (radio.attr("id") !== "anyBaggage") {
                $('.filter--baggage').addClass("selected")
                    .find('[data-toggle="dropdown"] span').html(radio.parent().find('label:last').html());
                scope.addFilterParams({
                    name: 'baggage',
                    value: radio.attr("id")
                });
            } else {
                $('.filter--baggage').removeClass("selected")
                    .find('[data-toggle="dropdown"] span').html(/*locale.filterStops.stops*/);
                scope.unsetFilterParams('baggage');
            }
        });

        $(".filter--baggage .js-clear-filter").on("click", function(e) {
            e.stopImmediatePropagation();
            $('.filter--baggage').removeClass("selected")
                .find('[data-toggle="dropdown"] span').html(/*locale.filterStops.stops*/);
            $(".filter--baggage .custom-radio").find('input[type="radio"]:first').prop('checked', true);
            scope.unsetFilterParams("baggage");
        });
    };

    this.filterDuration = function() {
        //= DURATION
        // get max duration
        var max = 0, min = Number.MAX_SAFE_INTEGER;

        $('.search-result__quote').each(function(idx){
            var durations = $(this).data('duration');
            durations.forEach(function(duration) {
                if (duration > max)
                    max = duration;
                if (duration < min)
                    min = duration;
            })
        });

        if (max < min) {
            min = 0;
        }

        var sliderDuration = $('#duration-slider')[0],
            filterDuration = ".filter--duration",
            jsFilterReset = ".filter--duration .js-filter-reset",
            jsClearFilter = ".filter--duration i.js-clear-filter";
        noUiSlider.create(sliderDuration, {
            start: [max],
            connect: [true, false],
            tooltips: {
                to: function(value){
                    return scope.helper.toHHMM(value * 60);
                }
            },
            step: 30,
            range: {
                'min': min,
                'max': max
            }
        });

        sliderDuration.noUiSlider.on('change', function(value, handle, unencoded, tap) {
            if (tap) {
                $(jsFilterReset).removeClass('hidden');
                $(filterDuration).addClass('selected').find('a[data-toggle="dropdown"] span').html(locale.filterGeneral.under + ": " + scope.helper.toHHMM(value * 60));
                scope.addFilterParams({name: 'duration', value: value * 1});
            }
        });

        $(jsClearFilter + ", " + jsFilterReset).on("click", function(e) {
            e.stopImmediatePropagation();
            $(jsFilterReset).addClass('hidden');
            $(filterDuration).removeClass('selected').find('a[data-toggle="dropdown"] span').html(locale.filterDuration.duration);
            sliderDuration.noUiSlider.reset();
            scope.unsetFilterParams("duration");
        });

        sliderDuration.noUiSlider.on('end', function(value) {
            $(jsFilterReset).removeClass('hidden');
            $(filterDuration).addClass('selected').find('a[data-toggle="dropdown"] span').html(locale.filterGeneral.under + ": " + scope.helper.toHHMM(value * 60));
            scope.addFilterParams({name: 'duration', value: value * 1});
        });

        sliderDuration.noUiSlider.on('update', function (values, handle) {
            $('#duration-slider-label').html(scope.helper.toHHMM(values[handle] * 60));
        });
        //=# DURATION
    };

    this.filterPrice = function() {
        //= PRICE
        // get min, max price
        var max = Math.round($('#price-slider')[0].getAttribute('data-max')),
            min = Math.round($('#price-slider')[0].getAttribute('data-min')), step = 10;
        max = Math.floor(max / step +1) * step;
        if (max < min)
            min = 0;

        var sliderPrice = $('#price-slider')[0],
            filterPrice = ".filter--price",
            jsFilterReset = ".filter--price .js-filter-reset",
            jsClearFilter = ".filter--price i.js-clear-filter";
        noUiSlider.create(sliderPrice, {
            start: [max],
            connect: [true, false],
            tooltips: [true],
            step: step,
            range: {
                'min': min,
                'max': max
            }
        });

        sliderPrice.noUiSlider.on('change', function(value, handle, unencoded, tap) {
            if (tap) {
                $(jsFilterReset).removeClass('hidden');
                $(filterPrice).addClass('selected').find('a[data-toggle="dropdown"] span').html(locale.filterGeneral.under + ": " + value);
                scope.addFilterParams({name: 'price', value: value * 1});
            }
        });

        $(jsClearFilter + ", " + jsFilterReset).on("click", function(e) {
            e.stopImmediatePropagation();
            $(jsFilterReset).addClass('hidden');
            $(filterPrice).removeClass('selected').find('a[data-toggle="dropdown"] span').html(locale.filterPrice.price);
            sliderPrice.noUiSlider.reset();
            scope.unsetFilterParams("price");
        });

        sliderPrice.noUiSlider.on('end', function(value) {
            $(jsFilterReset).removeClass('hidden');
            $(filterPrice).addClass('selected').find('a[data-toggle="dropdown"] span').html(locale.filterGeneral.under + ": " + value);
            scope.addFilterParams({name: 'price', value: value * 1});
        });

        sliderPrice.noUiSlider.on('update', function (values, handle) {
            $('#price-slider-label').html(locale.filterPrice.to + " " + locale.currency.symbol + values[handle]);
        });
        //=# PRICE
    };

    this.filterAirline = function() {
        $('.js-all-airlines').parent().click(function () {
            $('.js-all-airlines').attr("checked", !$('.js-all-airlines').attr("checked"));
            var val = [];
            if ($('.js-all-airlines').prop('checked') === true) {
                $('#filter-airlines').find('.js-filter-airl-item input[type="checkbox"]').each(function () {
                    $(this).prop('checked', true);
                    val.push($(this).attr("id"));
                });
            }
            else {
                $('#filter-airlines').find('.js-filter-airl-item input[type="checkbox"]').each(function () {
                    $(this).prop('checked', false);
                });
            }
            scope.addFilterParams({
                name: 'airline',
                value: val
            });
        });

        $('.js-filter-only').click(function () {
            var inputOnly = $(this).siblings('input[type="checkbox"]');
            inputOnly.prop('checked', true);
            $('#filter-airlines').find('.js-filter-airl-item input[type="checkbox"]').not(inputOnly).each(function () {
                $(this).prop('checked', false);
            });
            scope.addFilterParams({
                name: 'airline',
                value: [inputOnly.attr("id")]
            });
        });

        $('.js-filter-airl-item input[type="checkbox"]').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });

        $('.js-filter-airl-item label').on('click', function() {
            var inputOnly = $(this).siblings('input[type="checkbox"]');
            inputOnly.prop('checked', !inputOnly.prop('checked'));
            var val = [];
            $('#filter-airlines').find('.js-filter-airl-item input[type="checkbox"]:checked').each(function () {
                val.push($(this).attr("id"));
            });
            $('.js-all-airlines').attr("checked", $('#filter-airlines').find('.js-filter-airl-item input[type="checkbox"]').length === val.length);
            scope.addFilterParams({
                name: 'airline',
                value: val
            });
        });
    };

    this.filterFareType = function() {
        $('.js-filter-fareType-item input[type="checkbox"]').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });

        $('.js-filter-fareType-item label').on('click', function() {
            var inputOnly = $(this).siblings('input[type="checkbox"]');
            inputOnly.prop('checked', !inputOnly.prop('checked'));
            var val = [];
            $('#filter-fareType').find('.js-filter-fareType-item input[type="checkbox"]:checked').each(function () {
                val.push($(this).attr("id"));
            });
            scope.addFilterParams({
                name: 'fareType',
                value: val
            });
        });
    };

    this.filterTravelTimeSelectedTitleText = function() {
        var headerText = 'Time';
        if (Object.keys(filterList.travelTime).length < 3) {
            var count = 0,
                headerTextData = [];

            for (var timeIndex in filterList.travelTime) {
                if (!filterList.travelTime.hasOwnProperty(timeIndex)) continue;
                var item = filterList.travelTime[timeIndex];
                if (item.depart) {
                    count++;
                    headerTextData.push({direction: "departure", data: item.depart});
                }
                if (item.arrival) {
                    count ++;
                    headerTextData.push({direction: "arrival", data: item.arrival});
                }
            }
            if (count < 3) {
                headerText = '<span class="filter__summary js-filter-summary">';
                for (var h=0; h<count; h++) {
                    headerText += '   <span class="filter__summary-time">' +
                        '       <i class="icn-take-' + (headerTextData[h].direction === "departure" ? 'off' : 'on') + '-white"></i>' +
                        scope.helper.toHHMM(headerTextData[h].data[0] * 60) + ' - ' + scope.helper.toHHMM(headerTextData[h].data[1] * 60) +
                        '   </span>';
                }
                headerText += '</span>';
            }
        }
        return headerText;
    };

    this.filterTravelTime = function() {

        var sliderFlightTime = [],
            filterTime = ".filter--travel-time",
            jsFilterReset = ".filter--travel-time .js-filter-reset",
            jsClearFilter = ".filter--travel-time i.js-clear-filter";

        if ($('[data-id="landing-slider-time"]').length) {
            var el = $('[data-id="landing-slider-time"]');
            for (var i=0, l=el.length; i<l; i++) {

                sliderFlightTime.push(el[i]);
                noUiSlider.create(el[i], {
                    start: [0, 1440],
                    connect: [false, true, false],
                    tooltips: [
                        {to: function(value) {return scope.helper.toHHMM(value * 60);}},
                        {to: function(value) {return scope.helper.toHHMM(value * 60);}}
                    ],
                    step: 60,
                    range: {
                        'min': 0,
                        'max': 1440
                    }
                });

                el[i].noUiSlider.on('update', function (values, handle) {
                    var selectedDate = scope.helper.toHHMM(values[0] * 60) + ' - ' + scope.helper.toHHMM(values[1] * 60);
                    $(this.target).parents('[data-id="landing-time"]').find('[data-id="landing-value-time"] span').html(selectedDate);
                });

                el[i].noUiSlider.on('change', function(values, handle, unencoded, tap, positions) {
                    if (tap) {
                        $(jsFilterReset).removeClass('hidden');
                        scope.addFilterParams({
                            name: 'travelTime',
                            value: values,
                            index: $(this.target).parents('.tab-pane').attr('data-index'),
                            direction: $(this.target).attr('data-direction')
                        });

                        $(filterTime).addClass('selected').find('a[data-toggle="dropdown"] span').html(scope.filterTravelTimeSelectedTitleText());
                    }
                });

                el[i].noUiSlider.on('end', function(values) {
                    $(jsFilterReset).removeClass('hidden');
                    scope.addFilterParams({
                        name: 'travelTime',
                        value: values,
                        index: $(this.target).parents('.tab-pane').attr('data-index'),
                        direction: $(this.target).attr('data-direction')
                    });
                    $(filterTime).addClass('selected').find('a[data-toggle="dropdown"] span').html(scope.filterTravelTimeSelectedTitleText());
                });

                $(jsClearFilter + ", " + jsFilterReset).on("click", function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    $(jsFilterReset).addClass('hidden');
                    $(filterTime).removeClass('selected').find('a[data-toggle="dropdown"] span').html(locale.time);
                    sliderFlightTime.forEach(function(item) {
                        item.noUiSlider.reset();
                    });
                    scope.unsetFilterParams("travelTime");
                });
            }
        }
    }

    this.filterInit = function() {
        //= fare filter
        scope.filterFareType();
        //= airline filter
        scope.filterAirline();
        //=# airline filter
        //= price filter
        scope.filterPrice();
        //=# price filter
        //= duration filter
        scope.filterDuration();
        //=# duration filter
        //= stops filter
        scope.filterStops();
        //= airline change
        scope.filterAirlineChange();
        //= baggage filter
        scope.filterBaggage();

        //= time filter
        if ($('.filter--travel-time .nav-tabs').length) {
            $('.filter--travel-time .nav-tabs').tab();
            $('.filter--travel-time .nav-tabs li a').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show')
            });
            $('.filter--travel-time .nav-tabs li:first a').click();
        }
        scope.filterTravelTime();
        //=# time filter
    };

    this.helper = {
        isJson: function(json) {
            if (json === null) return false;
            try {
                JSON.parse(json);
                return true;
            } catch(e) {
                return false;
            }
        },
        toHHMM: function(str, hideHours) {
            var sec_num = parseInt(str, 10); // don't forget the second param
            var hours   = Math.floor(sec_num / 3600);
            var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
            // var seconds = sec_num - (hours * 3600) - (minutes * 60);

            if (hours   < 10) {hours   = "0"+hours;}
            if (minutes < 10) {minutes = "0"+minutes;}
            // if (seconds < 10) {seconds = "0"+seconds;}
            if (("" + hours) === "00" && !!hideHours)
                return minutes + locale.timeTexts.minute;
            return hours + locale.timeTexts.hour + ' ' + minutes + locale.timeTexts.minute;
        },
        minutesOfDay: function(m){
            return m.getMinutes() + m.getHours() * 60;
        },
        dateBetweenToTimes: function(range, date) {
            var dt = scope.helper.dateFromString(date),
                minutes = scope.helper.minutesOfDay(dt);

            return minutes >= range[0] && minutes <= range[1];
        },
        dateFromString: function(str) {
            var a = $.map(str.split(/[^0-9]/), function(s) { return parseInt(s, 10) });
            return new Date(a[0], a[1]-1 || 0, a[2] || 1, a[3] || 0, a[4] || 0, a[5] || 0, a[6] || 0);
        }
    };
};