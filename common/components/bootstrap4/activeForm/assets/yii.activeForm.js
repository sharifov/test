;(function ($, window) {

    window.addRemoveErrorListenerToActiveFormField = function (formId, errorSummaryCssClass, errorCssClass, fieldErrorCssClass) {
        let form = $("#" + formId);
        $.each($('input, select, textarea', form), function () {
            if ($(this).attr('type') === 'hidden') {
                return;
            }
            if ($(this).is("input")) {
                $(this).on('input', function () {
                    activeFormRemoveErrorsMessages(form, errorSummaryCssClass, errorCssClass, fieldErrorCssClass);
                });
                $(this).on('change', function () {
                    activeFormRemoveErrorsMessages(form, errorSummaryCssClass, errorCssClass, fieldErrorCssClass);
                });
            } else if ($(this).is("select")) {
                $(this).on('change', function () {
                    activeFormRemoveErrorsMessages(form, errorSummaryCssClass, errorCssClass, fieldErrorCssClass);
                });
            } else if ($(this).is("textarea")) {
                $(this).on('input', function () {
                    activeFormRemoveErrorsMessages(form, errorSummaryCssClass, errorCssClass, fieldErrorCssClass);
                });
            }
        });
    };

    function activeFormRemoveErrorsMessages(form, errorSummaryCssClass, errorCssClass, fieldErrorCssClass) {
        form.find(errorSummaryCssClass).hide();
        form.find('.' + errorCssClass).each(function (index, el) {
            $(el).removeClass(errorCssClass);
        });
        form.find(fieldErrorCssClass).html('');
    }

})($, window);
