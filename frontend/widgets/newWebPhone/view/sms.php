<?php

use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Json;

/** @var View $this */
/** @var array $userPhones */

$listUrl = Url::to(['/sms/list-ajax']);
$sendUrl = Url::to(['/sms/send']);
$userPhonesJson =  Json::encode($userPhones);

$js = <<<JS

let userPhones = {$userPhonesJson};

var PhoneWidgetSms = {
    listUrl: '{$listUrl}',
    userPhones: userPhones,
    sendUrl: '{$sendUrl}',
    
    updateStatus(sms) {
        let wrapper = $(document).find('.web-phone-widget-sms-' + sms['id'] + '-status');
        this.clearStatusClass(wrapper);
        let statusClass = this.getStatusClass(sms['status']);
        if (statusClass) {
            wrapper.addClass(statusClass);
        }
    },
    
    getStatusClass(status) {        
        if (status === 1) {
            return ' status-new';
        }        
        if (status === 2) {
            return ' status-pending';
        }        
        if (status === 3) {
            return ' status-process';
        }        
        if (status === 4) {
            return ' status-cancel';
        }        
        if (status === 5) {
            return ' status-done';
        }
        if (status === 6) {
            return ' status-error';
        }
        if (status === 7) {
            return ' status-sent';
        }
        return '';
    },
    
    clearStatusClass(wrapper) {
        wrapper.removeClass('status-new').removeClass('status-pending').removeClass('status-process')
               .removeClass('status-cancel').removeClass('status-done').removeClass('status-error')
               .removeClass('status-sent');
    },
    
    encode(str) {
        return btoa(JSON.stringify(str));
    },
    
    decode(str) {
        return JSON.parse(atob(str));
    },   

    modalSelectNumber(contactInfo) {
        let content = '';
        let selfObj = this;
        $.each(this.userPhones, function(i, phone) {
            content += '<span class="phone-widget-userPhones btn btn-success" data-contact-info="' + selfObj.encode(contactInfo) + '" data-user-phone="' + phone + '">' + phone + '</span>';
        });
        let modal = $('#modal-df'); 
        modal.find('.modal-body').html(content);
        modal.find('.modal-title').html('Select your phone number');
        modal.modal('show');
    },
    
    loadSms(data) {
        let selfObj = this;
        let container = $(".widget-phone__messages-modal");
        $(".phone-widget__tab").addClass('ovf-hidden');
        container.html("").show();
        container.append(this.getPreloader());
        $.ajax({
                type: 'POST',
                url: this.listUrl,
                data: data,
                dataType: 'json',
            }
        )
        .done(function(result) {
            container.html("");
            if (!result.success) {
                container.append(selfObj.parseErrors(result.errors));
                return false;
            }
            container.append(selfObj.loadContactData(result.contact, result.userPhone));
            container.append(selfObj.loadMessages(result));
            container.append(selfObj.loadSendForm(data));
            selfObj.simpleBarInit();
            selfObj.scrollDown();
        })
        .fail(function() {
            container.html("");
            new PNotify({
                title: "Get sms",
                type: "error",
                text: 'Server Error. Try again later',
                hide: true
            });
        });
    },
    
    simpleBarInit() {
        let messagesModal = $(".messages-modal__messages-scroll");
        new SimpleBar(messagesModal[0]);
    },
    
    getMessagesContainerName(contactId, contactPhone, userPhone) {
         return 'phone-widget-sms-messages-container-' + contactId + '-' + contactPhone.substr(1) + '-' + userPhone.substr(1);
    },
    
    loadMessages(data) {
        let content = '<div class="messages-modal__messages-scroll"><div class="messages-modal__body ' + this.getMessagesContainerName(data.contact['id'], data.contact['phone'], data.userPhone) + '">';
        let selfObj = this;
        $.each(data.sms, function(groupName, group) {
             content += '<span class="section-separator">' + groupName + '</span>';
             content += '<ul class="messages-modal__msg-list" data-group="' + groupName + '">';
             $.each(group, function(i) {
                 content += selfObj.loadItem(group[i]);
             });
             content += '</ul>';
         });
        content += '</div></div';
        return content;
    },
    
    loadItem(sms) {
        let destinationClass = '';
        // type = 1 (Out)
        if (sms['type'] === 1) {
             destinationClass =  ' pw-msg-item--user';
        }
        return  '<li class="messages-modal__msg-item pw-msg-item' + destinationClass + '">' +
                            '<div class="pw-msg-item__avatar">' +
                                '<div class="agent-text-avatar">' +
                                      '<span>' + sms['avatar'] + '</span>' +
                                '</div>' +
                            '</div>' +
                            '<div class="pw-msg-item__msg-main">' +
                                '<div class="pw-msg-item__data">' +
                                    '<span class="pw-msg-item__name">' + sms['name'] + '</span>' +
                                    '<span class="pw-msg-item__timestamp">' + sms['time'] + '</span>' +
                                '</div>' +
                                '<div class="pw-msg-item__msg-wrap' + this.getStatusClass(sms['status']) + ' web-phone-widget-sms-' + sms['id'] + '-status">' +
                                    '<p class="pw-msg-item__msg">' + sms['text'] + '</p>' + 
                                    '</div>' +
                            '</div>' +
                        '</li>';
    },
        
    loadContactData(contact, userPhone) {
        return  this.loadBackToContacts() +
            '<div class="modal-messaging__contact-info">' +
                '<div class="modal-messaging__info-list">' +
                    '<div class="modal-messaging__info-item" style="margin-bottom:0">SMS to <span class="modal-messaging__contact-name">' + contact['name'] + '</span></div>' +
                    '<span class="modal-messaging__info-number">' + contact['phone'] + '</span>' +
                    '<div class="modal-messaging__info-item" style="margin-bottom:0">From: <span class="modal-messaging__contact-name">' + userPhone + '</span></div>' +
                '</div>' +
            '</div>';
    },
    
    loadSendForm(contact) {
        return '<div class="messages-modal__footer">' +
                    '<form id="phone-widget-send-sms-form" action="' + this.sendUrl + '" method="post">' +
                        '<div class="messages-modal__input-group">' +
                            '<input name="text" type="text" class="messages-modal__msg-input" placeholder="Your Message" required>' +
                            '<input name="contactId" type="hidden" value="' + contact['contactId'] + '">' +
                            '<input name="contactPhone" type="hidden" value="' + contact['contactPhone'] + '">' +
                            '<input name="userPhone" type="hidden" value="' + contact['userPhone'] + '">' +
                            '<button class="messages-modal__send-btn">' +
                                '<i class="fa fa-paper-plane"></i>' +
                            '</button>' +
                        '</div>' +
                    '</form>' +
                '</div>';
    },
    
    getPreloader() {
        return '<div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div>';
    },
    
    parseErrors(errors) {
        let content = this.loadBackToContacts() + '<div style="padding: 20px;color:red"><p><strong>Errors:</strong></p>';
        $.each(errors, function(i, error) {
            $.each(error, function(j, err) {
                content += '<div style="padding: 5px 0 5px 0;">' + err + '</div>';
            });
        });
        content += '</div>';
        return content;
    },
    
    loadBackToContacts() {
        return '<a href="#" class="widget-modal__close">' +
                '<i class="fa fa-arrow-left"></i>' +
                'Back to contacts</i>' +
            '</a>';
    },
    
    sendButtonStart() {
        $(document).find('.messages-modal__msg-input').prop("disabled", "disabled");
        $(document).find('.messages-modal__send-btn').prop("disabled", "disabled").html('<i class="fa fa-spinner fa-spin"></i>');
    },
    
    sendButtonFinish() {
        $(document).find('.messages-modal__msg-input').prop("disabled", false).val("");
        $(document).find('.messages-modal__send-btn').prop("disabled", false).html('<i class="fa fa-paper-plane"></i>');
    },
    
    addSms(data) {
        let added = false;
        let selfObj = this;
        let containerClass = '.' + this.getMessagesContainerName(data.contact['id'], data.contact['phone'], data.userPhone);
        let container = $(document).find(containerClass);
        if (!container) {
            return false;
        }
        container.find(".messages-modal__msg-list").map(function() {
             if (data.sms['group'] === $(this).data("group")) {
                 $(this).append(selfObj.loadItem(data.sms));
                 added = true;
                 return false;
             }
         });
         if (!added) {
             let content = '<span class="section-separator">' + data.sms['group'] + '</span>';
             content += '<ul class="messages-modal__msg-list" data-group="' + data.sms['group'] + '">';
             content += selfObj.loadItem(data.sms);
             content += '</ul>';
             container.append(content);
         }
         this.scrollDown();        
    },
    
    scrollDown() {
         let scroll = $('.messages-modal__messages-scroll').find($('.simplebar-content-wrapper'))[0];
         $(scroll).scrollTop($(scroll)[0].scrollHeight);
    }
    
};

$(document).on('click', '.js-trigger-messages-modal', function() {
    let data = {"contactId": $(this).data('contact-id'), "contactPhone": $(this).data('contact-phone')};
    let countPhones = PhoneWidgetSms.userPhones.length;
    if (countPhones < 1) {
        new PNotify({title: "Get sms messages", type: "error", text: 'Not found user phones.', hide: true});
        return false;
    }
    if (countPhones > 1) {
        PhoneWidgetSms.modalSelectNumber(data);
        return false;
    }
    data['userPhone'] = PhoneWidgetSms.userPhones[0];
    PhoneWidgetSms.loadSms(data);
});

$(document).on('click', '.phone-widget-userPhones', function() {
    $('#modal-df').modal('hide');
    let data = PhoneWidgetSms.decode($(this).data('contact-info'));
    data['userPhone'] = $(this).data('user-phone');        
    PhoneWidgetSms.loadSms(data);         
});

$(document).on('click', '.messages-modal__send-btn', function(e) {
  e.preventDefault();
  let form = $("#phone-widget-send-sms-form");
  let data = form.serializeArray();
  PhoneWidgetSms.sendButtonStart();
  $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: data,
            dataType: 'json',
        }
    )
    .done(function(data) {
         PhoneWidgetSms.sendButtonFinish();
         if (!data.success) {
            let content = '';
            $.each(data.errors, function(i, error) {
                $.each(error, function(j, err) {
                    content += err + '<br>';
                });
            });
            new PNotify({title: "Send sms", type: "error", text: content, hide: true});
            return false;
         }
         PhoneWidgetSms.addSms(data);         
    })
    .fail(function() {
        PhoneWidgetSms.sendButtonFinish();
        new PNotify({title: "Send sms", type: "error", text: 'Server Error. Try again later', hide: true});
    });
    return false;
});
        
JS;

$this->registerJs($js);
