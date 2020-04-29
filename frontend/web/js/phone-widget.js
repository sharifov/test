$(document).ready(function() {
    $phoneTabAnchor = $('[data-toggle-tab]');

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

    $phoneTabAnchor.on('click', function() {
        $current = '#' + $(this).data('toggle-tab');

        $phoneTabAnchor.removeClass('is_active');
        $(this).addClass('is_active');
        $('.phone-widget__tab').removeClass('is_active');
        $($current).addClass('is_active')

    })

    // $('.phone-widget__tab').each(function(i, el) {
    //     var simpleBar = new SimpleBar(el);
    //     simpleBar.getContentElement();
    // })

    $('.contact-modal-info__close').on('click', function() {
        $('.contact-modal-info').hide()
    })

    $('.js-toggle-contact-info').on('click', function() {
        $('.contact-modal-info').show()
    })

    var contactModal = $('.contact-modal-info');
    var blockSuggestion = $('.suggested-contacts');
    // var suggestions = new SimpleBar(blockSuggestion[0]);
    // var modalScroll = new SimpleBar(contactModal[0])
    // modalScroll.getContentElement()
    // suggestions.getContentElement()



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

        $(this).removeClass('is-shown')
    });

    // $('.call-pane__dial-number').on('keyup', delay(function() {
    //     if ($(this).val() !== '') {
    //
    //
    //
    //         $('.suggested-contacts').addClass('is_active');
    //         $('.call-pane__dial-clear-all').addClass('is-shown')
    //     } else {
    //         $('.suggested-contacts').removeClass('is_active');
    //         $('.call-pane__dial-clear-all').removeClass('is-shown')
    //     }
    // }, 800));

    $('.call-pane__correction').on('click', function(e) {
        e.preventDefault();

        var currentVal = $('.call-pane__dial-number').val();
        $('.call-pane__dial-number').val(currentVal.slice(0, -1))
        if (currentVal.length === 1) {
            $('.suggested-contacts').removeClass('is_active');
            $('.call-pane__dial-clear-all').removeClass('is-shown');
        }
    })

    $('.js-edit-mode').on('click', function(e) {
        e.preventDefault();

        if ($(this).hasClass('is-editing')) {
            $(this).removeClass('is-editing');
            $('.contact-full-info').removeClass('edit-mode')
            $(this).find('span').text('Edit');
            $('.contact-full-info .form-control').each(function(i, el) {
                $(el).attr('readonly', true);
                $(el).attr('disabled', true);
            });
            return;
        }

        // $('.contact-full-info .form-control').on('click', function(e){
        //   e.preventDefault()
        // })

        $('.contact-full-info').addClass('edit-mode');
        $(this).addClass('is-editing');

        $('.contact-full-info .form-control').each(function(i, el) {
            $(el).attr('readonly', false);
            $(el).attr('disabled', false);
        });

        $('.is-editing').find('span').text('Save')

    });

    $('.select-contact-type').on('change', function() {
        $(this).closest('.form-control-wrap').attr('data-type', $(this).val().toLowerCase())
    })

    $('.js-toggle-phone-widget').on('click', function(e) {
        e.preventDefault();

        $('.phone-widget').toggleClass('is_active');
        $(this).toggleClass('is-mirror')
    })

    $('.phone-widget__close').on('click', function(e) {
        e.preventDefault();

        $('.phone-widget').toggleClass('is_active')
        $('.js-toggle-phone-widget').toggleClass('is-mirror');
    })

    $('.js-call-tab-trigger').on('click', function(e) {
        e.preventDefault();

        $('.contact-modal-info').hide();
        $('.phone-widget__tab').removeClass('is_active')
        $('#tab-phone').addClass('is_active');
        $('[data-toggle-tab]').removeClass('is_active')
        $('[data-toggle-tab="tab-phone"]').addClass('is_active')

    })

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

    window.newWidgetCancelCall = function () {
        $('.phone-widget-icon').removeClass('is-on-call');
        $('.phone-widget-icon').removeClass('is-pending');
        $('.call-pane__call-btns').removeClass('is-on-call');
        $('.call-pane__call-btns').removeClass('is-pending')
    }
});