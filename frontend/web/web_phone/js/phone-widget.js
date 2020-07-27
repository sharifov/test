$(document).ready(function() {
    window.widgetIcon = new handleWidgetIcon();
    widgetIcon.init();


    $phoneTabAnchor = $('[data-toggle-tab]');
    var historySimpleBar;

    function delay(callback, ms) {
        var timer = 0;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    }

    var tabHistoryLoaded = false;
    $phoneTabAnchor.on("click", function () {
        $current = "#" + $(this).data("toggle-tab");

        $phoneTabAnchor.removeClass("is_active");
        $(this).addClass("is_active");
        $(".phone-widget__tab").removeClass("is_active");
        $($current).addClass("is_active");

        $('.widget-modal').hide();

        $('.collapsible-container').collapse('hide');

        filterCalls.reset();

        if ($(this).data("toggle-tab") === 'tab-history') {
            if (!tabHistoryLoaded) {
                tabHistoryLoaded = true;
                $.ajax({
                    url: '/call-log/ajax-get-call-history',
                    type: 'post',
                    data: {},
                    dataType: 'json',
                    beforeSend: function() {
                        $($current).append('<div class="wg-history-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
                    },
                    success: function (data) {
                        $('#tab-history .simplebar-content').append(data.html);
                        historySimpleBar.recalculate();
                        $('#tab-history').attr('data-page', data.page);
                    },
                    complete: function (data) {
                        $($current).find('.wg-history-load').remove();
                    },
                    error: function (xhr, error) {
                    }
                });
            }
            let countMissedCalls = parseInt($(this).attr('data-missed-calls'));
            if (countMissedCalls > 0) {
                PhoneWidgetCall.requestClearMissedCalls();
            }
        }

        if ($current === '#tab-contacts') {
            if (PhoneWidgetContacts.fullListIsEmpty()) {
            }

            if ($(this).hasClass('js-add-to-conference')) {
                window.localStorage.setItem('contactSelectableState', 1);
                $('.contacts-list').addClass('contacts-list--selection');
                $('.selection-amount').show();

            } else {
                window.localStorage.setItem('contactSelectableState', 0);
                $('.contacts-list').removeClass('contacts-list--selection');
                $('.submit-selected-contacts').slideUp(10);
                $('.selection-amount').hide();
            }

            PhoneWidgetContacts.requestFullList();

        }

    });

    function initLazyLoadHistory(simpleBar) {

        var ajax = false;
        simpleBar.getScrollElement().addEventListener('scroll', function(e) {
            if((e.target.scrollTop + e.target.clientHeight) === e.target.scrollHeight && !ajax) {
                // ajax call get data from server and append to the div
                var page = $('#tab-history').attr('data-page');
                $.ajax({
                    url: '/call-log/ajax-get-call-history',
                    type: 'post',
                    data: {page: page, uid: userId},
                    dataType: 'json',
                    beforeSend: function() {
                        $($current).append('<div class="wg-history-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
                        ajax = true;
                    },
                    success: function (data) {
                        $('#tab-history .simplebar-content').append(data.html);
                        historySimpleBar.recalculate();
                        $('#tab-history').attr('data-page', data.page);
                        if (!data.rows) {
                            ajax = false;
                        }
                    },
                    complete: function () {
                        $($current).find('.wg-history-load').remove();
                    },
                    error: function (xhr, error) {
                    }
                });
            }
        });
    }

    $('.phone-widget__tab').each(function(i, el) {
        var simpleBar = new SimpleBar(el);
        simpleBar.getContentElement();

        if ($(el).attr('id') === 'tab-history') {
            initLazyLoadHistory(simpleBar);
            historySimpleBar = simpleBar;
        }

        if ($(el).attr('id') === 'tab-contacts' && typeof PhoneWidgetContacts !== 'undefined') {
           PhoneWidgetContacts.initLazyLoadFullList(simpleBar);
        }
    });

    




    $('.js-toggle-contact-info').on('click', function() {
        $('.contact-modal-info').show()
    })

    function addCC() {
        return '<input type="text" class="email-modal__contact-input additional-subj" placeholder="CC">'
    }

    function addBCC() {
        return '<input type="text" class="email-modal__contact-input additional-subj" placeholder="BCC">'
    }

    function clearEmailTab() {
        $('.subject-option__add').removeClass('added');
        $('.additional-subj').remove()
    }

    $('.subject-option__add').on('click', function() {
        if ($(this).hasClass('added')) {
            return;
        }
        switch ($(this).data('add-type')) {
            case 'cc':
                $('.email-modal__modal-input-list').append(addCC())
                break;

            case 'bcc':
                $('.email-modal__modal-input-list').append(addBCC())
                break;
        }
        $(this).addClass('added')
    })

    // var messagesModal = $(".messages-modal__messages-scroll");
    // var emailModal = $(".email-modal__messages-scroll");

    var elemScrollable = $('.scrollable-block');

    var additionalBar = $('.additional-bar__body');
    var contactModal = $(".contact-modal-info");
    var blockSuggestion = $(".suggested-contacts");
    // var msgModalScroll = new SimpleBar(messagesModal[0]);
    // var emailModalScroll = new SimpleBar(emailModal[0]);
    var suggestions = new SimpleBar(blockSuggestion[0]);
    var modalScroll = new SimpleBar(contactModal[0]);
    modalScroll.getContentElement();
    suggestions.getContentElement();
    // msgModalScroll.getContentElement();
    // emailModalScroll.getContentElement();
    // msgModalScroll.recalculate();
    $(additionalBar).each(function(i, el) {
        var elem = new SimpleBar(el);
        elem.getContentElement();
    })

    $('.toggle-bar-settings').on('click', function() {
        $('#bar-settings').slideToggle(150)
        $('#bar-logs').slideUp(150)
    })

    $('.additional-bar__close').on('click', function() {
        console.log($(this).parents('.additional-bar'))
        $(this).parents('.additional-bar').slideUp(150);
    })

    $('.toggle-bar-logs').on('click', function() {
        $('#bar-logs').slideToggle(150)
        $('#bar-settings').slideUp(150)
    })

    $('.additional-bar__close').on('click', function() {
        console.log($(this).parents('.additional-bar'))
        $(this).parents('.additional-bar').slideUp(150);
    })

    var activeSettingTab = $('.tab-trigger.active-tab').attr('href');

    $(activeSettingTab).show()

    $('.tab-trigger').on('click', function(e) {
        e.preventDefault()
        $('.tab-trigger').removeClass('active-tab');
        $(this).addClass('active-tab');

        $('.tabs__item').hide()
        $($(this).attr('href')).show()

    })


    $(elemScrollable).each(function(i, elem) {
        var el = new SimpleBar(elem);
        el.getContentElement();
    })



    //--------------------------------------------------------------------------

    // polyfill
    var AudioContext = window.AudioContext || window.webkitAudioContext || window.mozAudioContext;

    function Tone(context, freq1, freq2) {
        this.context = context;
        this.status = 0;
        this.freq1 = freq1;
        this.freq2 = freq2;
    }

    Tone.prototype.setup = function(){
        this.osc1 = context.createOscillator();
        this.osc2 = context.createOscillator();
        this.osc1.frequency.value = this.freq1;
        this.osc2.frequency.value = this.freq2;

        this.gainNode = this.context.createGain();
        this.gainNode.gain.value = 0.25;

        this.filter = this.context.createBiquadFilter();
        this.filter.type = "lowpass";
        this.filter.frequency = 8000;

        this.osc1.connect(this.gainNode);
        this.osc2.connect(this.gainNode);

        this.gainNode.connect(this.filter);
        this.filter.connect(context.destination);
    }

    Tone.prototype.start = function(){
        this.setup();
        this.osc1.start(0);
        this.osc2.start(0);
        this.status = 1;
    }

    Tone.prototype.stop = function(){
        this.osc1.stop(0);
        this.osc2.stop(0);
        this.status = 0;
    }

    var dtmfFrequencies = {
        "1": {f1: 697, f2: 1209},
        "2": {f1: 697, f2: 1336},
        "3": {f1: 697, f2: 1477},
        "4": {f1: 770, f2: 1209},
        "5": {f1: 770, f2: 1336},
        "6": {f1: 770, f2: 1477},
        "7": {f1: 852, f2: 1209},
        "8": {f1: 852, f2: 1336},
        "9": {f1: 852, f2: 1477},
        "✱": {f1: 941, f2: 1209},
        "0": {f1: 941, f2: 1336},
        "#": {f1: 941, f2: 1477},
        "+": {f1: 941, f2: 1497}
    }

    var context = new AudioContext();

    var dtmf = new Tone(context, 350, 440);
    var dialpadCurrentValue = null;
    var dialpadButtonTimer = null;

    $('.dial__btn').on("mousedown touchstart", function(e){
        e.preventDefault();

        var keyPressed = $(this).val();
        dialpadCurrentValue = keyPressed;
        dialpadButtonTimer = setInterval(function () {
            if (dialpadCurrentValue === '0') {
                let currentVal = $('.call-pane__dial-number').val();
                if (currentVal) {
                    currentVal = currentVal.substring(0, currentVal.length - 1);
                    currentVal = currentVal + '+';
                    $('.call-pane__dial-number').val(currentVal);
                }
            }
            clearInterval(dialpadButtonTimer);
        }, 700);
        var frequencyPair = dtmfFrequencies[keyPressed];

        // this sets the freq1 and freq2 properties
        dtmf.freq1 = frequencyPair.f1;
        dtmf.freq2 = frequencyPair.f2;

        if (dtmf.status == 0){
            dtmf.start();
        }

        //let btnVal = $(this).val();
        let currentVal = $('.call-pane__dial-number').val();

        $('.call-pane__dial-number').val(currentVal + keyPressed);
        $('.call-pane__dial-clear-all').addClass('is-shown');
        //$('.suggested-contacts').addClass('is_active');
        $('.call-pane__dial-number').focus();
    });

    $(window).on("mouseup touchend", function(){
        if (typeof dtmf !== "undefined" && dtmf.status){
            dtmf.stop();
        }
        clearInterval(dialpadButtonTimer);
    });

    //---------------------------------------------------

    $('.call_pane_dialpad_clear_number').on('click', function(e) {
        e.preventDefault();
        $('.call-pane__dial-number').val('').attr('readonly', false).prop('readonly', false);
        $('#call-to-label').text('');
        $('.suggested-contacts').removeClass('is_active');

        $('.dial__btn').attr('disabled', false).removeClass('disabled');

        // $(this).removeClass('is-shown')
    });
    $('.call_pane_dialpad_clear_number_disabled').on('click', function(e) {
        e.preventDefault();
        $('.call-pane__dial-number').val('').attr('readonly', true).prop('readonly', true);
        $('#call-to-label').text('');
        $('.suggested-contacts').removeClass('is_active');
    });

    $('.call-pane__correction').on('click', function(e) {
        e.preventDefault();

        var currentVal = $('.call-pane__dial-number').val();
        $('.call-pane__dial-number').val(currentVal.slice(0, -1))
        if (currentVal.length === 1) {
            $('.suggested-contacts').removeClass('is_active');
            // $('.call-pane__dial-clear-all').removeClass('is-shown');
        }
    })

    $(".js-edit-mode").on("click", function (e) {
        e.preventDefault();

        if ($(this).hasClass("is-editing")) {
            $(this).removeClass("is-editing");
            $('.contact-modal-info').find(".contact-full-info").removeClass("edit-mode");
            $(this).find("span").text("Edit");
            $('.contact-modal-info').find(".contact-full-info .form-control").each(function (i, el) {
                $(el).attr("readonly", true);
                $(el).attr("disabled", true);
            });
            return;
        }

        $('.contact-modal-info').find(".contact-full-info").addClass("edit-mode");
        $(this).addClass("is-editing");

        $('.contact-modal-info').find(".contact-full-info .form-control").each(function (i, el) {
            $(el).attr("readonly", false);
            $(el).attr("disabled", false);
        });

        $(".is-editing").find("span").text("Save");
    });

    $(".select-contact-type").on("change", function () {
        $(this)
            .closest(".form-control-wrap")
            .attr("data-type", $(this).val().toLowerCase());
    });

    $(".js-toggle-phone-widget").on("click", function (e) {
        e.preventDefault();

        $(".phone-widget").toggleClass("is_active");
        $(this).toggleClass("is-mirror");

    });

    $(".phone-widget__close").on("click", function (e) {
        e.preventDefault();

        $(".phone-widget").toggleClass("is_active");
        $(".js-toggle-phone-widget").toggleClass("is-mirror");
    });

    $(".js-call-tab-trigger").on("click", function (e) {
        e.preventDefault();

        $(".widget-modal").hide();
        $(".phone-widget__tab").removeClass("is_active");
        $("#tab-phone").addClass("is_active");
        $("[data-toggle-tab]").removeClass("is_active");
        $('[data-toggle-tab="tab-phone"]').addClass("is_active");
    });

    // presentational scripts
    var timeout;
    function callTimeout() {
        timeout = setTimeout(function() {
            $('.phone-widget-icon').removeClass('is-pending');
            $('.phone-widget-icon').addClass('is-on-call');
            $('.call-pane__call-btns').removeClass('is-pending');
            $('.call-pane__call-btns').addClass('is-on-call')
            $('.call-in-action__text').text('on call');
        }, 4000)
    }
    // $('.call-pane__start-call').on('click', function(e) {
    //     e.preventDefault();
    //
    // });

    // $('.messages-modal__send-btn').on('click', function() {
    //     // var scroll = $(msgModalScroll.getContentElement());
    //     var scroll = $('.messages-modal__messages-scroll').find($('.simplebar-content-wrapper'))[0];
    //
    //     $('.messages-modal__msg-list').append(appendMsg($('.messages-modal__msg-input').val()))
    //     $(scroll).scrollTop($(scroll)[0].scrollHeight)
    //
    //     $('.messages-modal__msg-input').val('')
    // });

    // function appendMsg(msg) {
    //     var time = new Date();
    //
    //     var node = '<li class="messages-modal__msg-item pw-msg-item pw-msg-item--user">'+
    //         '<div class="pw-msg-item__avatar">'+
    //         '<div class="agent-text-avatar">'+
    //         '<span>B</span>'+
    //         '</div>'+
    //         '</div>'+
    //         '<div class="pw-msg-item__msg-main">'+
    //         '<div class="pw-msg-item__data">'+
    //         '<span class="pw-msg-item__name">Me</span>'+
    //         '<span class="pw-msg-item__timestamp">' + time.getHours() + ':'+ time.getMinutes() +' PM</span>'+
    //         '</div>'+
    //         '<div class="pw-msg-item__msg-wrap">'+
    //         '<p class="pw-msg-item__msg">' + msg + '</p>'+
    //         '</div>'+
    //         '</div>'+
    //         '</li>';
    //     return node;
    // }

    // var data = {
    //     'selected': {
    //         'value': '+1-222-555-2222',
    //         'project': 'gtt',
    //         'id': 'dd-select'
    //     },
    //     'options': [
    //         {
    //             'value': '+1-222-555-4444',
    //             'project': 'flygtravel'
    //         },
    //         {
    //             'value': '+1-222-555-3333',
    //             'project': 'wowgateway'
    //         },
    //         {
    //             'value': '+1-222-555-2222',
    //             'project': 'gtt'
    //         },
    //         {
    //             'value': '+1-222-555-1111',
    //             'project': 'gtt2'
    //         }
    //     ]
    // }
    
    // var currentNumber = toSelect($('.custom-phone-select'), data, function() {
    //     console.log('here goes a callback')
    //     console.log(currentNumber.getData);
    // });
    
    $(document).on("click", ".widget-modal__close", function () {
        $(".widget-modal").hide();
        $(".phone-widget__tab").removeClass('ovf-hidden');
        $('.collapsible-container').collapse('hide');
        clearEmailTab()
    });

    var callsData = [
        {
            id: 1,
            state: 'inProgress',
            project: 'ovago',
            department: 'sales',
            length: 100,
            contact: {
                name: 'Geff Robertson1',
                company: 'LLC "DREAM TRAVEL"',
                number: '+123 321 234 432'
            }
        },
        {
            id: 2,
            state: 'hold',
            project: 'wowfare',
            department: 'sales',
            length: 30,
            contact: {
                name: 'New name',
                company: '"Rrtsa TRAVEL"',
                number: '+373 45 45'
            }
        },
        {
            id: 3,
            state: 'direct',
            project: 'arangrant',
            department: 'sales',
            length: 30,
            contact: {
                name: 'New name',
                company: '"Rrtsa TRAVEL"',
                number: '+373 45 45'
            }
        },
        {
            id: 4,
            state: 'general',
            project: 'hop2',
            department: 'sales',
            length: 30,
            contact: {
                name: 'New name',
                company: '"Rrtsa TRAVEL"',
                number: '+373 45 45'
            }
        }
    ];



    var callsObj = [
        {
            project: 'ovago',
            department: 'sales',
            id: 1,
            calls: [
                {
                    state: 'inProgress',
                    length: 6001,
                    id: 1,
                    contact: {
                        name: 'Geff Robertson1',
                        company: 'LLC "DREAM TRAVEL"',
                        number: '+123 321 234 432'
                    }
                },
                {
                    state: 'hold',
                    length: 2201,
                    id: 2,
                    contact: {
                        name: 'John Doe2',
                        company: 'LLC "DREAM TRAVEL"',
                        number: '+123 321 234 432'
                    }
                },
                {
                    state: 'direct',
                    length: 3201,
                    id: 3,
                    contact: {
                        name: 'John Doe6',
                        company: 'LLC "DREAM TRAVEL"',
                        number: '+123 321 234 432'
                    }
                },
                {
                    state: 'hold',
                    length: 6201,
                    id: 4,
                    contact: {
                        name: 'John Doe7',
                        company: 'LLC "DREAM TRAVEL"',
                        number: '+123 321 234 432'
                    }
                }
            ]
        },
        {
            project: null,
            department: 'SALES',
            id: 2,
            calls: [

                {
                    state: 'general',
                    length: 5301,
                    id: 2,
                    contact: {
                        name: 'John Doe4',
                        company: 'LLC "DREAM TRAVEL"',
                        number: '+123 321 234 432'
                    }
                },

                {
                    state: 'general',
                    length: 5301,
                    id: 3,
                    contact: {
                        name: 'John Doe5',
                        company: 'LLC "DREAM TRAVEL"',
                        number: '+123 321 234 432'
                    }
                },
                {
                    state: 'direct',
                    length: 5401,
                    id: 4,
                    contact: {
                        name: 'John Doe8',
                        company: 'LLC "DREAM TRAVEL"',
                        number: '+123 321 234 432'
                    }
                },
                {
                    state: 'direct',
                    length: 6241,
                    id: 1,
                    contact: {
                        name: 'Gerrombo Saltison3',
                        company: 'LLC "DREAM TRAVEL"',
                        number: '+123 321 234 432'
                    }
                },
            ]
        },
        {
            project: 'wowfare',
            department: null,
            id: 2,
            calls: [

                {
                    state: 'general',
                    length: 5301,
                    id: 2,
                    contact: {
                        name: 'John Doe4',
                        company: 'LLC "DREAM TRAVEL"',
                        number: '+123 321 234 432'
                    }
                },

                {
                    state: 'general',
                    length: 5301,
                    id: 3,
                    contact: {
                        name: 'John Doe5',
                        company: 'LLC "DREAM TRAVEL"',
                        number: '+123 321 234 432'
                    }
                },
                {
                    state: 'direct',
                    length: 5401,
                    id: 4,
                    contact: {
                        name: 'John Doe8',
                        company: 'LLC "DREAM TRAVEL"',
                        number: '+123 321 234 432'
                    }
                },
                {
                    state: 'direct',
                    length: 6241,
                    id: 1,
                    contact: {
                        name: 'Gerrombo Saltison3',
                        company: 'LLC "DREAM TRAVEL"',
                        number: '+123 321 234 432'
                    }
                },
            ]
        }
    ];


   var filterCalls = new callsFilter(callsObj);
    //filterCalls.init();
    


    // function phoneWidgetBehavior(elem) {
    //     var $main = $(elem),
    //         backElement = '.widget-modal__close',
    //         widgetModal = '.widget-modal',
    //         widgetTab = '.phone-widget__tab',
    //         collapsibleContainer = '.collapsible-container';

    //         var events = {
    //             pwBackAction: 'pw-back-action'
    //         }

    //         this.actionMapping = function(object) {
    //             return {
    //                 back: object.back
    //             }
    //         };

    //         function getElement(selector) {
    //             return $($main).find(selector)
    //         }

    //         function backAction() {
    //             getElement(widgetModal).hide();
    //             getElement(widgetTab).removeClass('ovf-hidden');
    //             getElement(collapsibleContainer).collapse('hide');
    //         }

    //         $($main).on('click', backElement, function() {
    //             backAction();
    //             $(backElement).trigger(events.pwBackAction);
                
    //         });

    //     return {
    //         control: $main
    //     };

    // }

    // var widget = phoneWidgetBehavior('.phone-widget');


    // $(widget.control).on('pw-back-action', function () {
    //     console.log('here is a event for back button');
    // })

});

function stateTimer () {
    var interval = null;

    return {
        start: function (el, timerStamp) {
            var sec = Math.floor(timerStamp % 60);
            var min = Math.floor((timerStamp - sec) / 60);
            var hr = Math.floor((timerStamp - min) / 60);

            interval = setInterval(function () {
                sec = Math.floor(timerStamp % 60);
                min = Math.floor(((timerStamp - sec) / 60) % 60);
                hr = Math.floor(timerStamp / 3600);

                if (timerStamp === 86399) {
                    timerStamp = 0;
                }

                if (parseInt(sec) < 10) {
                    sec = '0' + sec;
                }

                if (parseInt(min) < 10) {
                    min = '0' + min;
                }

                if (parseInt(hr) < 10) {
                    hr = '0' + hr;
                }

                timerStamp++
                $(el).html(hr + ':' + min + ':' + sec)

            }, 1000)
        },
        clear: function () {
            clearInterval(interval);
        }
    }


}

function formatPhoneNumber(phoneNumberString) {
    let cleaned = ('' + phoneNumberString).replace(/\D/g, '')
    let match = cleaned.match(/^(1|)?(\d{3})(\d{3})(\d{4})$/)
    if (match) {
        var intlCode = (match[1] ? '+1 ' : '')
        return [intlCode, '(', match[2], ') ', match[3], '-', match[4]].join('')
    }
    return null
}

function toSelect(elem, obj, cb) {
    var $element = $(elem),
        $toggle = '.dropdown-toggle',
        $option = '.dropdown-item',
        selectedNumber = '.current-number__selected-nr',
        selectedText = '.current-number__selected-project';
    optionClass = 'dropdown-item';

    var selected = 'optionselected';

    this.data = {
        value: obj.selected.value,
        project: obj.selected.project,
        projectId: obj.selected.projectId
    }

    this.primaryData = {
        value: obj.primary ? obj.primary.value || null : null,
        project: obj.primary ? obj.primary.project || null : null,
        projectId: obj.primary ? obj.primary.projectId || null : null
    };

    // nodes
    function selectedNode(value, project, id, projectId, length) {
        let chevronDown = '';
        if (length > 1) {
            chevronDown = '<i class="fa fa-chevron-down"></i>';
        }
        return (
            '<button value="' + value + '" data-info-project="' + project + '" data-info-project-id="'+ projectId +'" class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+
            '<small class="current-number__phone current-number__selected-nr">' + formatPhoneNumber(value) + '</small>'+
            '<span class="current-number__identifier current-number__selected-project">' + project + '</span>'+
            chevronDown +
            '</button>'
        );
    }

    function optionNode(optionList) {
        let arr = []
        if (optionList.length > 1) {
            optionList.forEach(function (el) {
                arr.push('<button class="dropdown-item" type="button" value="' + el.value + '" data-info-project="' + el.project + '" data-info-project-id="' + el.projectId + '">' +
                    '<small class="current-number__phone">' + formatPhoneNumber(el.value) + '</small>' +
                    '<span class="current-number__identifier">' + el.project + '</span>' +
                    '</button>')
            })
        }

        return arr;
    }

    function containerNode(selected, optionList) {
        let arr = optionNode(optionList).join('');
        let str = '<div class="dropdown">'+
            selected +
            '<div class="dropdown-menu" >' +
            arr +
            '</div>';
        // if (optionList.length > 1) {
        //     str = str + '<i class="fa fa-chevron-down"></i>';
        // }
        str = str + '</div>';

        return str;
    }

    function generateSelect(obj) {
        let length = obj.options.length;
        $element.append(
            containerNode(selectedNode(obj.selected.value, obj.selected.project, obj.selected.id, obj.selected.projectId, length), obj.options)
        )
    }

    function setValue(option) {
        this.data.value = option.val();
        this.data.project = option.attr('data-info-project');
        this.data.projectId = option.attr('data-info-project-id');
        $($element).trigger(selected);
    }

    this.getData = function() {
        return this.data;
    }

    this.setData = function () {
        return function (obj) {
            this.data.value = obj.value;
            this.data.project = obj.project;
            this.data.projectId = obj.projectId;
        }.bind(this);
    }

    this.setPrimaryData = function () {
        return function (obj) {
            console.log(obj);
            this.primaryData.value = obj.value;
            this.primaryData.project = obj.project;
            this.primaryData.projectId = obj.projectId;
        }.bind(this);
    }

    this.getPrimaryData = function () {
        return this.primaryData;
    }

    this.clearPrimaryData = function () {
        return function () {
            this.primaryData.value = null;
            this.primaryData.project = null;
            this.primaryData.projectId = null;
            return this;
        }.bind(this);
    }

    generateSelect(obj)

    $($element).on(selected, $($toggle), function(e) {
        var elem = e.target,
            $selectedNumber = $element.find(selectedNumber),
            $selectedText = $element.find(selectedText);

        $(elem).find($toggle).val(this.data.value);
        $selectedNumber.text(this.data.value);
        $selectedText.text(this.data.project);

        if (typeof cb === 'function') {
            cb.call(this);
        }

    }.bind(this));

    $($element).on('click', $option, function() {
        setValue($(this))
    });

    return {
        getData: this.getData(),
        setData: this.setData(),
        setPrimaryData: this.setPrimaryData(),
        getPrimaryData: this.getPrimaryData(),
        clearPrimaryData: this.clearPrimaryData()
    }

}

function handleWidgetIcon() {
    var $parent = $('.phone-widget-icon'),
        $inner = '.widget-icon-inner',
        animationClass = 'animate',
        initialNode;

    var interval = null;
    
    function createInitialIcon(type,status) {
        initialNode = '<div class="widget-icon-inner" data-wi-type="'+ type +'" data-wi-status="'+ status +'">'+
            '<div class="standby-phone">'+
            '<i class="fa fa-phone-volume icon-phone-answer"></i>' +
            '<div class="phone-widget-icon__state">'+
            '<span class="phone-widget-icon__ongoing"></span>'+
            '<span class="phone-widget-icon__text"></span>'+
            '<span class="phone-widget-icon__time"></span>'+
            '</div>'+
            '<i class="fa fa-phone icon-phone"></i>'+
            
            '</div>'+
            '</div>';

        $($parent).append(initialNode)
    }

    function stateTimer(el, timerStamp) {


        var sec = Math.floor(timerStamp % 60);
        var min = Math.floor((timerStamp - sec) / 60);
        var hr = Math.floor((timerStamp - min) / 60);
        
        interval = setInterval(function() {
            sec = Math.floor(timerStamp % 60);
            min = Math.floor(((timerStamp - sec) / 60) % 60);
            hr = Math.floor(timerStamp / 3600);
            
            if (timerStamp === 86399) {
                timerStamp = 0;
            }
            
            if (parseInt(sec) < 10) {
                sec = '0' + sec;
            }

            if (parseInt(min) < 10) {
                min = '0' + min;
            }

            if (parseInt(hr) < 10) {
                hr = '0' + hr;
            }

            timerStamp++
            $(el).html(hr + ':' + min + ':' + sec)

        }, 1000)

    }

    function updateIcon(props) {
        $($inner).removeClass(animationClass);
        var inner = '.widget-icon-inner',
            ongoing = '.phone-widget-icon__ongoing',
            text = '.phone-widget-icon__text',
            time = '.phone-widget-icon__time';

        if (props.timer) {
            $(time).html(null);
        }

        clearInterval(interval);

        $(inner).attr('data-wi-status', props.status);
        $(inner).attr('data-wi-type', props.type);
        $(ongoing).html(props.currentCalls);
        $(text).html(props.text);

        if (props.timer) {
            stateTimer(time, props.timerStamp)
        } else {
            $(time).html(null)
        }

        props = null;
        $($inner).addClass(animationClass);
    }

    return {
        init: function() {
            createInitialIcon('default', false) 
        },
        update: function(props) {
            updateIcon(props)
        }
    }
}

function callsFilter (object) {
    var queuesParent = '.queue-separator',
        queuesItem = '.queue-separator__item',
        listingParent = '.calls-separator',
        callsParent = '.call-in-progress',
        filterToggle = '.call-filter__toggle',
        listingItem = '.calls-separator__list-item',
        callItem = '.call-in-progress__list-item';

    // function getQueueItem (string, data) {
    //     var queueItem = '<li class="queue-separator__item" data-queue-type="' + data + '">' +
    //         '<div class="queue-separator__name">' + string + '</div>' +
    //         '<ul class="calls-separator"> </ul>' +
    //         '</li>';
    //     return queueItem;
    // }
    //
    // function getListingItem (props) {
    //
    //     function getProjectBinding (data) {
    //         if (data.project && data.department) {
    //             return '<div class="static-number-indicator">' +
    //                 '<span class="static-number-indicator__label">' + props.project + '</span>' +
    //                 '<i class="static-number-indicator__separator"></i>' +
    //                 '<span class="static-number-indicator__name">' + props.department + '</span>' +
    //                 '</div>'
    //         } else if (data.project && !data.department) {
    //             return '<div class="static-number-indicator">' +
    //                 '<span class="static-number-indicator__label">' + props.project + '</span>' +
    //                 '</div>'
    //         } else {
    //             return '<div class="static-number-indicator">' +
    //                 '<span class="static-number-indicator__name">Exteral contact</span>' +
    //                 '</div>'
    //         }
    //     }
    //
    //
    //
    //     var listing = '<li class="calls-separator__list-item" id="' + props.id + '">' +
    //         getProjectBinding(props) +
    //         '<ul class="call-in-progress">' +
    //         '</ul>' +
    //         '</li>';
    //
    //     return listing;
    //
    // }
    //
    // function getCallNode (props) {
    //
    //     var item = '<li class="call-in-progress__list-item" id="' + props.id + '">' +
    //         '<div class="call-in-progress__call-item call-list-item" data-call-status="' + props.state + '">' +
    //         '<div class="call-list-item__info">' +
    //         '<ul class="call-list-item__info-list call-info-list">' +
    //         '<li class="call-info-list__item">' +
    //         '<b class="call-info-list__contact-icon">' +
    //         '<i class="fa fa-user"></i>' +
    //         '</b>' +
    //         '<span class="call-info-list__name">' + props.contact.name + '</span>' +
    //         '</li>' +
    //         '<li class="call-info-list__item">' +
    //         '<span class="call-info-list__company">' + props.contact.company + '</span>' +
    //         '</li>' +
    //         '<li class="call-info-list__item">' +
    //         '<span class="call-info-list__number">' + props.contact.number + '</span>' +
    //         '</li>' +
    //         '</ul>' +
    //         '<div class="call-list-item__info-action call-info-action">' +
    //         '<span class="call-info-action__timer"></span>' +
    //         '<a href="#" class="call-info-action__more"><i class="fa fa-ellipsis-h"></i></a>' +
    //         '' +
    //         '</div>' +
    //         '<ul class="call-list-item__menu call-item-menu">' +
    //         '<li class="call-item-menu__list-item">' +
    //         '<a href="#" class="call-item-menu__close">' +
    //         '<i class="fa fa-chevron-right"></i>' +
    //         '</a>' +
    //         '</li>' +
    //         '<li class="call-item-menu__list-item">' +
    //         '<a href="#" class="call-item-menu__transfer">' +
    //         '<i class="fa fa-random"></i>' +
    //         '</a>' +
    //         '</li>' +
    //         '<li class="call-item-menu__list-item">' +
    //         '<a href="#" class="call-item-menu__transfer">' +
    //         '<i class="fa fa-pause"></i>' +
    //         '</a>' +
    //         '</li>' +
    //         '<li class="call-item-menu__list-item">' +
    //         '<a href="#" class="call-item-menu__transfer">' +
    //         '<i class="fas fa-phone-slash"></i>' +
    //         '</a>' +
    //         '</li>' +
    //         '</ul>' +
    //         '</div>' +
    //         '<div class="call-list-item__main-action">' +
    //         '<a href="#" class="call-list-item__main-action-trigger">' +
    //         '<i class="phone-icon phone-icon--start fa fa-phone"></i>' +
    //         '<i class="phone-icon phone-icon--end fa fa-phone-slash"></i>' +
    //
    //         '</a>' +
    //         '</div>' +
    //         '</div>' +
    //         '</li>';
    //
    //
    //     return item;
    // }
    //
    // function filterData (handler, dataObj) {
    //     var obj = JSON.parse(JSON.stringify(dataObj))
    //     var filtered = [];
    //
    //     for (const item in obj) {
    //         if (obj.hasOwnProperty(item)) {
    //             filtered.push(obj[item])
    //         }
    //     }
    //
    //     filtered.forEach(function (el, i) {
    //
    //         if ($(handler).attr('data-call-filter') === 'all') {
    //             return filtered;
    //         }
    //
    //         el.calls = el.calls.filter(function (call, i) {
    //             if (call.state === $(handler).attr('data-call-filter')) {
    //                 return call
    //             }
    //         })
    //     })
    //
    //     return filtered;
    // }
    //
    // function renderData (incomingData) {
    //     var refData = JSON.parse(JSON.stringify(incomingData));
    //     var callsList = [];
    //     var queues = [];
    //
    //
    //     $(queuesItem).detach();
    //     $(listingItem).detach();
    //
    //     refData.forEach(function (element, i) {
    //         element.calls.forEach(function (call) {
    //             if (queues.indexOf(call.state) === -1) {
    //                 queues.push(call.state)
    //             }
    //
    //             if (callsList.indexOf(call) === -1) {
    //                 callsList.push(call)
    //             }
    //         })
    //     })
    //
    //     callsList.forEach(function (call, i) {
    //         var timer = new stateTimer();
    //         timer.start($('.call-info-action__timer')[i], call.length);
    //
    //     })
    //
    //     function objRemaster(list, obj) {
    //         var data = {};
    //         var foo = [];
    //         var tmpArr;
    //
    //         list.forEach(function(listItem, i) {
    //
    //             data[listItem] = [];
    //             obj.forEach(function (objEl, i) {
    //                 var tmpArr = [];
    //                 objEl.calls.filter(function(call){
    //                     if (call.state === listItem) {
    //                         tmpArr.push(call)
    //                     }
    //                     return call
    //                 })
    //                 for (var key in data) {
    //                     data[key] = obj
    //                 }
    //                 foo.push(tmpArr)
    //
    //             })
    //
    //         })
    //
    //
    //         for (var key in data) {
    //
    //             data[key].forEach(function(obj) {
    //                 var arr = [];
    //                 obj.calls.filter(function(item, i) {
    //                     arr = obj.calls;
    //                     if (item.state !== key) {
    //                         // console.log(item)
    //                         // console.log(data[key],obj)
    //                         // obj.calls.splice(obj.calls.indexOf(item), 1)
    //                     }
    //                 })
    //
    //
    //             })
    //         }
    //         return data
    //
    //     }
    //
    //
    //     var queueName = {
    //         'hold': 'On Hold',
    //         'direct': 'Direct Calls',
    //         'general': 'General Line',
    //         'inProgress': 'Active'
    //     }
    //
    //
    //     var rData = objRemaster(queues, refData);
    //
    //     for (var key in rData) {
    //         $(queuesParent).append(getQueueItem(queueName[key], key))
    //
    //         var section = $('[data-queue-type="'+ key +'"]');
    //
    //         rData[key].forEach(function (listing) {
    //
    //             $(section).find(listingParent).append(getListingItem(listing))
    //
    //             listing.calls.forEach(function(call) {
    //
    //                 if ($(section).attr('data-queue-type') === call.state) {
    //                     $(section).find(listingItem).append(getCallNode(call))
    //                 }
    //             })
    //         })
    //     }
    //
    //
    //
    // }

    return {
        init: function () {
            // renderData(object);


            // function clearIndicators (target) {
            //     var markElement = $('.widget-line-overlay__queue-marker');
            //
            //     markElement.removeClass('tab-hold');
            //     markElement.removeClass('tab-direct');
            //     markElement.removeClass('tab-general');
            //     markElement.removeClass('tab-all');
            //
            //     switch ($(target).attr('data-call-filter')) {
            //         case 'hold':
            //             $('[data-queue-marker]').html('Calls On Hold');
            //             markElement.addClass('tab-hold')
            //             break;
            //         case 'direct':
            //             $('[data-queue-marker]').html('Direct Calls')
            //             markElement.addClass('tab-direct')
            //             break;
            //         case 'general':
            //             $('[data-queue-marker]').html('General Lines')
            //             markElement.addClass('tab-general')
            //             break;
            //         case 'all':
            //             $('[data-queue-marker]').html('Calls Queue')
            //             break;
            //     }
            // }

            // $(document).on('click', filterToggle, function (e) {
            //     e.preventDefault();
            //
            //     $('.widget-line-overlay').show();
            //     var activeClass = 'is-checked';
            //     var localObj = filterData($(this), object);
            //     renderData(localObj);
            //     $(filterToggle).removeClass(activeClass);
            //     $(this).addClass(activeClass);
            //     clearIndicators($(this));
            // });

            // $(document).on('click', filterToggle, function (e) {
            //     $('.widget-line-overlay').show();
            //
            //     e.preventDefault();
            //     var markElement = $('.widget-line-overlay__queue-marker');
            //
            //     var activeClass = 'is-checked';
            //     var localObj = filterData($(this), object)
            //     renderData(localObj);
            //
            //     $(filterToggle).removeClass(activeClass)
            //     $(this).addClass(activeClass)
            //
            //     clearIndicators($(this));
            // });

            // $(document).on('click', '.widget-line-overlay__show-all-queues', function (e) {
            //     e.preventDefault();
            //     $(filterToggle).addClass('is-checked');
            //     var localObj = filterData($(this), object)
            //     renderData(localObj);
            //
            //    // clearIndicators($(this));
            //
            // })

        },
        reset: function () {
            $('.widget-line-overlay').hide();
            var activeClass = 'is-checked';
            $(filterToggle).removeClass(activeClass);
        }
    }
}

$(document).on('click', '.call-item-menu__close', function (e) {
    e.preventDefault();
    $(this).closest('.call-list-item').removeClass('call-list-item--menu')
})

$(document).on('click', '.call-info-action__more', function (e) {
    e.preventDefault();
    $(this).closest('.call-list-item').addClass('call-list-item--menu')
})

$(document).on('click', '.call-details__nav-btn--more', function(e) {
    e.preventDefault();
    $('.conference-call-details').addClass('is_active')
});

$(document).on('click', '.call-details__nav-btn--back', function(e) {
    e.preventDefault();
    $('.conference-call-details').removeClass('is_active')
});