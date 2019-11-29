/** v.1.0 **/

;( function () {
    window.pjaxOffFormSubmit = function pjaxOffFormSubmit(selector) {
        if (typeof selector === 'string' && $(selector).length) {
            $(document).off('submit', selector + ' form[data-pjax]');
            console.log('Event removed');
        }
    };
})();
