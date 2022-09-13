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
                createNotify('Error', xhr.responseText, 'error');
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

    window.enableTimer = function () {
        $('.enable-timer').each( function (i, e) {
            let seconds = $(e).attr('data-seconds');

            $(e).timer({format: '%d %H:%M:%S', seconds: seconds}).timer('start');
        });
    };

    window.starTimers = function () {
        $(".timer").each(function(index) {
            var sec = $( this ).data('sec');
            var control = $( this ).data('control');
            var format = $( this ).data('format');

            $(this).timer({countdown: true, format: format, duration: sec}).timer(control);
        });
    };

    window.startTooltips = function () {
        $('.js-tooltip').tooltip({
            placement: 'bottom'
        });
    };

})(window, $);

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

$(document).ready( function () {
    document.addEventListener("visibilitychange", function () {
        if (document.visibilityState === 'hidden') {
            localStorage.setItem('previousPage', $(document)[0].baseURI);
        }
    })
});
