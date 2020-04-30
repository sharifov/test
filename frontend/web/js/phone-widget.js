$(document).ready(function() {
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
                    url: '/call/ajax-get-call-history',
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
        }
    });

    function initLazyLoadHistory(simpleBar) {

        var ajax = false;
        simpleBar.getScrollElement().addEventListener('scroll', function(e) {
            if(e.target.scrollTop === e.target.scrollTopMax && !ajax) {
                // ajax call get data from server and append to the div
                var page = $('#tab-history').attr('data-page');
                $.ajax({
                    url: '/call/ajax-get-call-history',
                    type: 'post',
                    data: {page: page},
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
    })

    $(document).on("click", ".widget-modal__close", function () {
        $(".widget-modal").hide();
        $(".phone-widget__tab").removeClass('ovf-hidden');
        $('.collapsible-container').collapse('hide');
        clearEmailTab()
    });

    $('.js-toggle-contact-info').on('click', function() {
        $('.contact-modal-info').show()
    })

    $(".js-trigger-messages-modal").on("click", function () {
        $(".messages-modal").show();
        $(".phone-widget__tab").addClass('ovf-hidden');
    });

    $(".js-trigger-email-modal").on("click", function () {
        $(".email-modal").show();
        $(".phone-widget__tab").addClass('ovf-hidden');
    });

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

    $('.dial__btn').on('click', function(e) {
        e.preventDefault();
        var currentVal = $('.call-pane__dial-number').val();
        $('.call-pane__dial-number').val(currentVal + $(this).val());
        $('.call-pane__dial-clear-all').addClass('is-shown');
        $('.suggested-contacts').addClass('is_active');
        $('.call-pane__dial-number').focus()

    });

    $('.call-pane__dial-clear-all').on('click', function(e) {
        e.preventDefault();
        $('.call-pane__dial-number').val('')
        $('.suggested-contacts').removeClass('is_active');

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

    $('.call-pane__end-call').on('click', function(e) {
        e.preventDefault();

        if (device) {
            updateAgentStatus(connection, false, 1);
            device.disconnectAll();

            newWidgetCancelCall();

            clearTimeout(timeout)
        }
    })

    $('.messages-modal__send-btn').on('click', function() {
        // var scroll = $(msgModalScroll.getContentElement());
        var scroll = $('.messages-modal__messages-scroll').find($('.simplebar-content-wrapper'))[0];

        $('.messages-modal__msg-list').append(appendMsg($('.messages-modal__msg-input').val()))
        $(scroll).scrollTop($(scroll)[0].scrollHeight)

        $('.messages-modal__msg-input').val('')
    });

    function appendMsg(msg) {
        var time = new Date();

        var node = '<li class="messages-modal__msg-item pw-msg-item pw-msg-item--user">'+
            '<div class="pw-msg-item__avatar">'+
            '<div class="agent-text-avatar">'+
            '<span>B</span>'+
            '</div>'+
            '</div>'+
            '<div class="pw-msg-item__msg-main">'+
            '<div class="pw-msg-item__data">'+
            '<span class="pw-msg-item__name">Me</span>'+
            '<span class="pw-msg-item__timestamp">' + time.getHours() + ':'+ time.getMinutes() +' PM</span>'+
            '</div>'+
            '<div class="pw-msg-item__msg-wrap">'+
            '<p class="pw-msg-item__msg">' + msg + '</p>'+
            '</div>'+
            '</div>'+
            '</li>';
        return node;
    }

    window.newWidgetCancelCall = function () {
        $('.phone-widget-icon').removeClass('is-on-call');
        $('.phone-widget-icon').removeClass('is-pending');
        $('.call-pane__call-btns').removeClass('is-on-call');
        $('.call-pane__call-btns').removeClass('is-pending')
    }
});