/** v.1.0 **/

;(function (window, $) {
    window.pjaxOffFormSubmit = function pjaxOffFormSubmit(selector) {
        if (typeof selector === 'string' && $(selector).length) {
            $(document).off('submit', selector + ' form[data-pjax]');
        }
    };

    window.helper = {
        toHHMM: function (str, hideHours) {
            var sec_num = parseInt(str, 10);
            var hours = Math.floor(sec_num / 3600);
            var minutes = Math.floor((sec_num - (hours * 3600)) / 60);

            if (hours < 10) {
                hours = "0" + hours;
            }
            if (minutes < 10) {
                minutes = "0" + minutes;
            }
            if (("" + hours) === "00" && !!hideHours) {
                return minutes + 'm';
            }
            return hours + 'h' + ' ' + minutes + 'm';
        }
    };

    $('body').on('click', '.btn-modal-show', function (e) {
        e.preventDefault();

        let url = $(this).data('url');
        let title = $(this).data('title');
        let modalId = $(this).data('modal-id');
        let modal = $('#' + modalId);

        modal.find('.modal-body').html('');
        modal.find('.modal-title').html(title);
        modal.find('.modal-body').load(url, function (response, status, xhr) {
            //$('#preloader').addClass('d-none');
            if (status == 'error') {
                alert(response);
            } else {
                modal.modal({
                    // backdrop: 'static',
                    show: true
                });
            }
        });
    });

    window.pjaxReload = function (obj) {
        if (typeof obj !== 'object') {
            console.error('Type of provided param is not Object');
            return false;
        }

        var defaultOptions = {
            container: '',
            push: false,
            replace: false,
            async: false,
            timeout: 2000
        };

        var options = $.extend(defaultOptions, obj);

        if (!options.container) {
            console.error('Pjax container is not provided');
            return false;
        }

        $.pjax.reload(options);
    };

    $(document).on('click', '.wg-call', function (e) {
        e.preventDefault();

        let phone = $(this).data('phone-number');
        let title = $(this).data('title');
        let widgetBtn = $('.js-toggle-phone-widget');
        if (widgetBtn.length) {
            $('.phone-widget').addClass('is_active')
            $('.js-toggle-phone-widget').addClass('is-mirror');
            insertPhoneNumber(phone, title);
        }
    });

    window.enableTimer = function () {
        $('.enable-timer').each( function (i, e) {
            let seconds = $(e).attr('data-seconds');

            $(e).timer({format: '%H:%M:%S', seconds: seconds}).timer('start');
        });
    }

})(window, $);

function insertPhoneNumber(phone, title) {
    $('#call-pane__dial-number').val(phone).attr('readonly', 'readonly');
    if (title && title.length > 0) {
        $("#call-to-label").text(title);
    }
    soundNotification("button_tiny");
    $('.dial__btn').attr('disabled', 'disabled').addClass('disabled');
}

function soundNotification(fileName = 'button_tiny', volume = 0.3) {
    let audio = new Audio('/js/sounds/' + fileName + '.mp3');
    audio.volume = volume;
    audio.play();
}

function soundDisconnect() {
    soundNotification('disconnect_sound', 0.3);
}

function soundConnect() {
    soundNotification('connect_sound', 0.3);
}

let incomingAudio = new Audio('/js/sounds/incoming_sound.mp3');
incomingAudio.volume = 0.3;
incomingAudio.loop = true;
