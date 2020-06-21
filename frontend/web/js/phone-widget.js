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

        if ($(el).attr('id') === 'tab-contacts') {
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

    $('.dial__btn').on("mousedown touchstart", function(e){
        e.preventDefault();

        var keyPressed = $(this).val();
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
    });

    //---------------------------------------------------

    $('.call-pane__dial-clear-all').on('click', function(e) {
        e.preventDefault();
        $('.call-pane__dial-number').val('').attr('readonly', false).prop('readonly', false);
        $('#call-to-label').text('');
        $('.suggested-contacts').removeClass('is_active');

        $('.dial__btn').attr('disabled', false).removeClass('disabled');

        // $(this).removeClass('is-shown')
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

    $(document).on('click', '#cancel-active-call', function(e) {
        hangup($('#cancel-active-call'));
    });

    $(document).on('click', '#cancel-outgoing-call', function(e) {

        e.preventDefault();
        let btn = $(this);
        let callId = btn.attr('data-call-id');
        if (!callId) {
            new PNotify({title: "Hangup", type: "warning", text: "Please try again after some seconds.", hide: true});
            return false;
        }
        hangupOutgoingCall(btn, callId);

    });

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

    // nodes
    function selectedNode(value, project, id, projectId) {
        return (
            '<button value="' + value + '" data-info-project="' + project + '" data-info-project-id="'+ projectId +'" class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+
            '<small class="current-number__phone current-number__selected-nr">' + formatPhoneNumber(value) + '</small>'+
            '<span class="current-number__identifier current-number__selected-project">' + project + '</span>'+
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
        if (optionList.length > 1) {
            str = str + '<i class="fa fa-chevron-down"></i>';
        }
        str = str + '</div>';

        return str;
    }

    function generateSelect(obj) {
        $element.append(
            containerNode(selectedNode(obj.selected.value, obj.selected.project, obj.selected.id, obj.selected.projectId), obj.options)
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
