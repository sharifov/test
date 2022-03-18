let PhoneWidgetEmail = function () {

    let sendUrl = '';
    let userEmails = {};

    function init(sendUrlInit, userEmailsInit) {
        sendUrl = sendUrlInit;
        userEmails = userEmailsInit;
    }

    function getUserEmails() {
        return userEmails;
    }

    function encode(str) {
        return btoa(JSON.stringify(str));
    }

    function decode(str) {
        return JSON.parse(atob(str));
    }

    function showModalSelectNumber(contact, contactEmail) {
        let content = '';
        $.each(getUserEmails(), function (i, email) {
            content += '<span class="phone-widget-userEmails btn btn-success" style="margin-left: 0;margin-right: 7px" data-contact-email="' + contactEmail +  '" data-contact="' + contact + '" data-user-email="' + email + '">' + email + '</span>';
        });
        let modal = $('#modal-df');
        modal.find('.modal-body').html(content);
        modal.find('.modal-title').html('Select your email address');
        modal.modal('show');
    }

    function showPreloader() {
        $(document).find(".email-modal").append('<div class="wg-history-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
    }

    function hidePreloader() {
        $(document).find(".email-modal").find('.wg-history-load').remove();
    }

    function parseErrors(errors) {
        let content = '';
        $.each(errors, function (i, error) {
            $.each(error, function (j, err) {
                content += err + '<br>';
            });
        });
        return content;
    }

    function getBackToContacts() {
        return '<a href="#" class="widget-modal__close"><i class="fa fa-arrow-left"></i>Back to contacts</i></a>';
    }

    function show(contact, contactEmail, user) {
        let container = $(document).find(".email-modal");
        container.html("");
        let content = getBackToContacts() +
            '    <div class="modal-messaging__contact-info">' +
            '        <div class="modal-messaging__info-list">' +
            '            <div class="modal-messaging__info-item">Email to <span class="modal-messaging__contact-name">' + contact.name + '</span></div>' +
            '            <span class="modal-messaging__info-number">' + contactEmail + '</span>' +
            '            <div class="modal-messaging__info-item" style="margin-bottom:0">Email From: <span class="modal-messaging__contact-name">' + user.email + '</span></div>' +
            '        </div>' +
            '    </div>' +
            '' +
            '    <div class="email-modal__messages-scroll">' +
            '        <div class="email-modal__body">' +
                        '<form id="phone-widget-send-email-form" action="' + sendUrl + '" method="post">' +
            '               <input name="contactType" type="hidden" value="' + contact.type + '">' +
            '               <input name="contactId" type="hidden" value="' + contact.id + '">' +
            '               <input name="contactEmail" type="hidden" value="' + contactEmail + '">' +
            '               <input name="userEmail" type="hidden" value="' + user.email + '">' +
            '            <div class="email-modal__input-group">' +
            '                <div class="email-modal__subject-block">' +
            '                    <div class="email-modal__modal-input-list"> <input type="text" name="subject" class="email-modal__contact-input" placeholder="Subject"> </div>' +
            // '                    <ul class="subject-option">' +
            // '                        <li class="subject-option__add" data-add-type="cc">Add CC</li>' +
            // '                        <li class="subject-option__add" data-add-type="bcc">Add BCC</li>' +
            // '                    </ul>' +
            '                </div>' +
            '                <textarea class="email-modal__msg-input" placeholder="Your Message" name="text" cols="30" rows="10"></textarea>' +
            '            </div>' +
            '            <button class="email-modal__send-btn"><span>SEND</span><i class="fa fa-paper-plane"></i></button>' +
            '            </form>' +
            '        </div>' +
            '    </div>';
            container.append(content);
            container.show();
            $(".phone-widget__tab").addClass('ovf-hidden');
    }

    function send(form) {
        let data = form.serializeArray();
        showPreloader();
        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: data,
            dataType: 'json',
        })
            .done(function (data) {
                hidePreloader();
                if (!data.success) {
                    let content = parseErrors(data.errors);
                    createNotifyByObject({title: "Send email", type: "error", text: content, hide: true});
                    return false;
                }
                form.find(".email-modal__contact-input").val("");
                form.find(".email-modal__msg-input").val("");
                let message = 'Success';
                if (data.message) {
                    message = data.message;
                }
                createNotifyByObject({title: "Send email", type: "success", text: message, hide: true});
            })
            .fail(function () {
                hidePreloader();
                createNotifyByObject({title: "Send email", type: "error", text: 'Server Error. Try again later', hide: true});
            });
    }

    return {
        init: init,
        getUserEmails: getUserEmails,
        showModalSelectNumber: showModalSelectNumber,
        decode: decode,
        show: show,
        send: send
    }

}();

$(document).on("click", ".js-trigger-email-modal", function () {
    let countEmails = PhoneWidgetEmail.getUserEmails().length;
    if (countEmails < 1) {
        createNotifyByObject({title: "Send email", type: "error", text: 'Not found user emails.', hide: true});
        return false;
    }

    if (countEmails > 1) {
        PhoneWidgetEmail.showModalSelectNumber($(this).data('contact'), $(this).data('contact-email'));
        return false;
    }

    let contact = PhoneWidgetEmail.decode($(this).data('contact'));
    let contactEmail = $(this).data('contact-email');
    let user = {"email": PhoneWidgetEmail.getUserEmails()[0]};
    PhoneWidgetEmail.show(contact, contactEmail, user);
});

$(document).on('click', '.phone-widget-userEmails', function () {
    $('#modal-df').modal('hide');
    let contact = PhoneWidgetEmail.decode($(this).data('contact'));
    let contactEmail = $(this).data('contact-email');
    let user = {"email": $(this).data('user-email')};
    PhoneWidgetEmail.show(contact, contactEmail, user);
});

$(document).on('click', '.email-modal__send-btn', function (e) {
    e.preventDefault();
    let form = $(document).find("#phone-widget-send-email-form");
    PhoneWidgetEmail.send(form);
});
