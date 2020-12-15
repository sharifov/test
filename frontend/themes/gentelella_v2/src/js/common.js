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
        let userId = $(this).data('user-id');
        let widgetBtn = $('.js-toggle-phone-widget');
        if (widgetBtn.length) {
            $('.phone-widget').addClass('is_active')
            $('.js-toggle-phone-widget').addClass('is-mirror');
            insertPhoneNumber({
                'formatted': phone,
                'title': title,
                'user_id': userId,
                'phone_to': phone
            });
        }
    });

    window.enableTimer = function () {
        $('.enable-timer').each( function (i, e) {
            let seconds = $(e).attr('data-seconds');

            $(e).timer({format: '%d %H:%M:%S', seconds: seconds}).timer('start');
        });
    }

})(window, $);

/* data = {
    formatted,
    title,
    user_id,
    phone_to,
    phone_from,
    project_id,
    department_id,
    client_id,
    source_type_id,
    lead_id,
    case_id
} */
function insertPhoneNumber(data) {
    $('#call-pane__dial-number').val((data.formatted ? data.formatted : '')).attr('readonly', 'readonly');
    if (data.title && data.title.length > 0) {
        $("#call-to-label").text(data.title);
    } else {
        $("#call-to-label").text('');
    }
    $('#call-pane__dial-number-value')
        .attr('data-user-id', data.user_id ? data.user_id : '')
        .attr('data-phone-to', data.phone_to ? data.phone_to : '')
        .attr('data-phone-from', data.phone_from ? data.phone_from : '')
        .attr('data-is-request-from', data.is_request_from ? data.is_request_from : '')
        .attr('data-request-call-sid', data.request_call_sid ? data.request_call_sid : '')
        .attr('data-project-id', data.project_id ? data.project_id : '')
        .attr('data-department-id', data.department_id ? data.department_id : '')
        .attr('data-client-id', data.client_id ? data.client_id : '')
        .attr('data-source-type-id', data.source_type_id ? data.source_type_id : '')
        .attr('data-lead-id', data.lead_id ? data.lead_id : '')
        .attr('data-case-id', data.case_id ? data.case_id : '');

    soundNotification("button_tiny");
    $('.dialpad_btn_init').attr('disabled', 'disabled').addClass('disabled');
    $('.call-pane__correction').attr('disabled', 'disabled');
}

function insertPhoneNumberFrom(phone) {
    $('#call-pane__dial-number-value').attr('data-phone-from', phone);
}

$(document).on('input', '#call-pane__dial-number', function (e) {
    resetDialNumberData();
});

function resetDialNumberData() {
    $('#call-pane__dial-number-value')
        .attr('data-user-id', '')
        .attr('data-phone-to', '')
        .attr('data-phone-from', '')
        .attr('data-is-request-from', '')
        .attr('data-request-call-sid', '')
        .attr('data-project-id', '')
        .attr('data-department-id', '')
        .attr('data-client-id', '')
        .attr('data-source-type-id', '')
        .attr('data-lead-id', '')
        .attr('data-case-id', '');
}

function reserveDialButton()
{
    $('#btn-new-make-call').html('<i class="fa fa-spinner fa-spin"> </i>').attr('disabled', true);
}

function freeDialButton()
{
    $('#btn-new-make-call').html('<i class="fas fa-phone"> </i>').attr('disabled', false);
}

function soundNotification(fileName = 'button_tiny', volume = 0.3) {
    let audio = new Audio('/js/sounds/' + fileName + '.mp3');
    audio.volume = volume;
    let promise = audio.play();
    //Structure if in this case is used as a trick to hide DOM Exception in terminal
    if (promise !== undefined) {
        promise.then(_ => {
            // Autoplay started!
        }).catch(error => {
            // Autoplay was prevented.
            // Show a "Play" button so that user can start playback.
        });
    }
}

function soundDisconnect() {
    soundNotification('disconnect_sound', 0.3);
}

function soundConnect() {
    soundNotification('connect_sound', 0.99);
}

let incomingAudio = new Audio('/js/sounds/incoming_call.mp3');
incomingAudio.volume = 0.3;
incomingAudio.loop = true;

$(document).ready( function () {
    document.addEventListener("visibilitychange", function () {
        if (document.visibilityState === 'hidden') {
            localStorage.setItem('previousPage', $(document)[0].baseURI);
        }
    })
});
