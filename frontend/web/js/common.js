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

})(window, $);

function soundNotification(fileName = 'button_tiny', volume = 0.3) {
    let audio = new Audio('/js/sounds/' + fileName + '.mp3');
    audio.volume = volume;
    audio.play();
}
