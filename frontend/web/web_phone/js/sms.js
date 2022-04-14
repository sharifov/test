let PhoneWidgetSms = function () {

    let listUrl = '';
    let sendUrl = '';
    let userPhones = {};
    let statuses = {
        1: '<span class="pw-msg-item__status"> <i class="fa fa-check"></i> </span>', //new
        2: '<span class="pw-msg-item__status"> <i class="fa fa-check"></i> </span>', //pending
        3: '<span class="pw-msg-item__status"> <i class="fa fa-check"></i> </span>', //process
        4: '<span class="pw-msg-item__status"> <i class="fa fa-check"></i> </span>', //cancel
        5: '<span class="pw-msg-item__status pw-msg-item__status--delivered"> <i class="fa fa-check-double"></i> </span>', //done
        6: '<span class="pw-msg-item__status pw-msg-item__status--error"> <i class="fa fa-exclamation-circle"></i> </span>', //error
        7: '<span class="pw-msg-item__status"> <i class="fa fa-check"></i> </span>', //sent
        8: '<span class="pw-msg-item__status"> <i class="fa fa-check"></i> </span>', //queued
    };

    function init(listUrlInit, sendUrlInit, userPhonesInit) {
        listUrl = listUrlInit;
        sendUrl = sendUrlInit;
        userPhones = userPhonesInit;
    }

    function getUserPhones() {
        return userPhones;
    }

    function getSmsIconStatus(sms) {
        //console.log(sms.status);
        if (typeof statuses[sms.status] === 'undefined') {
            return '';
        }
        return statuses[sms.status];
    }

    function getSmsStatusId(sms) {
        return 'web-phone-widget-sms-' + sms.id;
    }

    function updateStatus(sms) {
        let container = $(document).find('.' + getSmsStatusId(sms));
        if (container) {
            container.html(getSmsIconStatus(sms));
        }
    }

    function encode(str) {
        return btoa(JSON.stringify(str));
    }

    function decode(str) {
        return JSON.parse(atob(str));
    }

    function showModalSelectNumber(contact) {
        let content = '';
        $.each(getUserPhones(), function (i, phone) {
            content += '<span class="phone-widget-userPhones btn btn-success" style="margin-left: 0;margin-right: 7px" data-contact="' + encode(contact) + '" data-user-phone="' + phone + '">' + phone + '</span>';
        });
        let modal = $('#modal-df');
        modal.find('.modal-body').html(content);
        modal.find('.modal-title').html('Select your phone number');
        modal.modal('show');
    }

    function loadSmses(contact, user) {
        let container = $(".widget-phone__messages-modal");
        let data = {"contactId": contact.id, "contactPhone": contact.phone, "contactType": contact.type, "userPhone": user.phone};

        $(".phone-widget__tab").addClass('ovf-hidden');
        container.html("").show();
        container.append(getPreloader());
        $.ajax({
                type: 'POST',
                url: listUrl,
                data: data,
                dataType: 'json',
            })
            .done(function (data) {
                container.html("");
                if (!data.success) {
                    container.append(parseErrors(data.errors));
                    return false;
                }
                let content = getContactData(data.contact, data.user) +
                            '<div class="messages-modal__messages-scroll">' +
                                '<div class="messages-modal__body ' + getSmsesContainerName(data.contact, data.user) + '"></div>' +
                            '</div>' + getSendForm(data.contact, data.user);
                container.append(content);
                addSmses(data.smses, getSmsesContainer(data.contact, data.user));
                simpleBarInit();
            })
            .fail(function (data) {
                container.html("");
                let text = 'Server Error. Try again later';
                if (data.status && data.status === 403) {
                    text = 'Access denied'
                }
                createNotifyByObject({title: "Get sms", type: "error", text: text, hide: true});
            });
    }

    function simpleBarInit() {
        let scroll = $(document).find(".messages-modal__messages-scroll");
        new SimpleBar(scroll[0]);
        scrollDown();
    }

    function scrollDown() {
        let scroll = $(document).find('.messages-modal__messages-scroll').find($('.simplebar-content-wrapper'))[0];
        if (scroll) {
            $(scroll).scrollTop($(scroll)[0].scrollHeight);
        }
    }

    function getSmsesContainerName(contact, user) {
        return 'phone-widget-sms-messages-container-' + contact.id + '-' + processPhone(contact.phone) + '-' + contact.type + '-' + processPhone(user.phone);
    }

    function processPhone(phone) {
        return phone.substr(1);
    }

    function addSmses(smses, container) {
        $.each(smses, function (index, sms) {
            addSms(sms, container);
        });
    }

    function addSms(sms, container) {
        if (!container) {
            return false;
        }

        let added = false;

        container.find(".messages-modal__msg-list").map(function () {
            if (sms.group === $(this).data("group")) {
                $(this).append(getSms(sms));
                added = true;
                return false;
            }
        });

        if (!added) {
            let content = '<span class="section-separator">' + sms.group + '</span>';
            content += '<ul class="messages-modal__msg-list" data-group="' + sms.group + '">';
            content += getSms(sms);
            content += '</ul>';
            container.append(content);
        }
    }

    function getSms(sms) {
        let typeClass = '';
        // type = 1 (Out)
        if (sms.type === 1) {
            typeClass = ' pw-msg-item--user';
        }

        return '<li class="messages-modal__msg-item pw-msg-item' + typeClass + '">' +
                    '<div class="pw-msg-item__avatar">' +
                        '<div class="agent-text-avatar">' +
                            '<span>' + sms.avatar + '</span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="pw-msg-item__msg-main">' +
                        '<div class="pw-msg-item__data">' +
                            '<span class="pw-msg-item__name">' + sms.name + '</span>' +
                            '<span class="pw-msg-item__timestamp">' + sms.time + '</span>' +
                            '<span class="' + getSmsStatusId(sms) + '">' + getSmsIconStatus(sms) + '</span>' +
                        '</div>' +
                        '<div class="pw-msg-item__msg-wrap">' +
                            '<p class="pw-msg-item__msg">' + sms.text + '</p>' +
                        '</div>' +
                    '</div>' +
                '</li>';
    }

    function getContactData(contact, user) {
        return getBackToContacts() +
                '<div class="modal-messaging__contact-info">' +
                    '<div class="modal-messaging__info-list">' +
                        '<div class="modal-messaging__info-item" style="margin-bottom:0">SMS to <span class="modal-messaging__contact-name">' + contact.name + '</span></div>' +
                        '<span class="modal-messaging__info-number">' + contact.phone + '</span>' +
                        '<div class="modal-messaging__info-item" style="margin-bottom:0">From: <span class="modal-messaging__contact-name">' + user.phone + '</span></div>' +
                    '</div>' +
                '</div>';
    }

    function getSendForm(contact, user) {
        return '<div class="messages-modal__footer">' +
                    '<form id="phone-widget-send-sms-form" action="' + sendUrl + '" method="post">' +
                        '<div class="messages-modal__input-group">' +
                                '<input name="text" type="text" class="messages-modal__msg-input" placeholder="Your Message">' +
                                '<input name="contactType" type="hidden" value="' + contact.type + '">' +
                                '<input name="contactId" type="hidden" value="' + contact.id + '">' +
                                '<input name="contactPhone" type="hidden" value="' + contact.phone + '">' +
                                '<input name="userPhone" type="hidden" value="' + user.phone + '">' +
                                '<button class="messages-modal__send-btn"><i class="fa fa-paper-plane"></i></button>' +
                        '</div>' +
                    '</form>' +
                '</div>';
    }

    function getPreloader() {
        return '<div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div>';
    }

    function parseErrors(errors) {
        let content = getBackToContacts() + '<div style="padding: 20px;color:red"><p><strong>Errors:</strong></p>';
        $.each(errors, function (i, error) {
            $.each(error, function (j, err) {
                content += '<div style="padding: 5px 0 5px 0;">' + err + '</div>';
            });
        });
        content += '</div>';
        return content;
    }

    function getBackToContacts() {
        return '<a href="#" class="widget-modal__close"><i class="fa fa-arrow-left"></i>Back to contacts</i></a>';
    }

    function sendStart() {
        $(document).find('.messages-modal__msg-input').prop("disabled", "disabled");
        $(document).find('.messages-modal__send-btn').prop("disabled", "disabled").html('<i class="fa fa-spinner fa-spin"></i>');
    }

    function sendFinish() {
        $(document).find('.messages-modal__msg-input').prop("disabled", false).val("");
        $(document).find('.messages-modal__send-btn').prop("disabled", false).html('<i class="fa fa-paper-plane"></i>');
    }

    function getSmsesContainer(contact, user) {
        let containerClass = '.' + getSmsesContainerName(contact, user);
        let container = $(document).find(containerClass);

        if (!container) {
            return false;
        }

        return container;
    }

    function socket(data) {
        if (data.command === 'update_status') {
            updateStatus(data.sms);
            return true;
        }
        if (data.command === 'add') {
            addSms(data.sms, getSmsesContainer(data.contact, data.user));
            scrollDown();
            return true;
        }
    }

    return {
        init: init,
        getUserPhones: getUserPhones,
        showModalSelectNumber: showModalSelectNumber,
        loadSmses: loadSmses,
        addSms: addSms,
        decode: decode,
        sendStart: sendStart,
        sendFinish: sendFinish,
        getSmsesContainer: getSmsesContainer,
        scrollDown: scrollDown,
        socket: socket
    }

}();

$(document).on('click', '.js-trigger-messages-modal', function () {
    let countPhones = PhoneWidgetSms.getUserPhones().length;
    if (countPhones < 1) {
        createNotifyByObject({title: "Get sms messages", type: "error", text: 'Not found user phones.', hide: true});
        return false;
    }
    let contact = {"id": $(this).data('contact-id'), "phone": $(this).data('contact-phone'), "type": $(this).data('contact-type')};
    if (countPhones > 1) {
        PhoneWidgetSms.showModalSelectNumber(contact);
        return false;
    }
    let user = {"phone": PhoneWidgetSms.getUserPhones()[0]};
    PhoneWidgetSms.loadSmses(contact, user);
});

$(document).on('click', '.phone-widget-userPhones', function () {
    $('#modal-df').modal('hide');
    let contact = PhoneWidgetSms.decode($(this).data('contact'));
    let user = {"phone": $(this).data('user-phone')};
    PhoneWidgetSms.loadSmses(contact, user);
});

$(document).on('click', '.messages-modal__send-btn', function (e) {
    e.preventDefault();
    let form = $("#phone-widget-send-sms-form");
    let data = form.serializeArray();
    PhoneWidgetSms.sendStart();
    $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: data,
            dataType: 'json',
        })
        .done(function (data) {
            PhoneWidgetSms.sendFinish();
            if (!data.success) {
                let content = '';
                $.each(data.errors, function (i, error) {
                    $.each(error, function (j, err) {
                        content += err + '<br>';
                    });
                });
                createNotifyByObject({title: "Send sms", type: "error", text: content, hide: true});
                return false;
            }
            PhoneWidgetSms.addSms(data.sms, PhoneWidgetSms.getSmsesContainer(data.contact, data.user));
            PhoneWidgetSms.scrollDown();
        })
        .fail(function () {
            PhoneWidgetSms.sendFinish();
            createNotifyByObject({title: "Send sms", type: "error", text: 'Server Error. Try again later', hide: true});
        });
    return false;
});
