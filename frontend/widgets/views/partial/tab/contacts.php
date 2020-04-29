<?php

/** @var View $this */

?>

<div class="phone-widget__tab " id="tab-contacts">
    <div class="contacts__search-wrap">
        <label class="contacts__icon" for="">
            <i class="fa fa-search"></i>
        </label>
        <?php

        use yii\bootstrap4\Html;
        use yii\web\View;
        use yii\widgets\ActiveForm;

        $form = ActiveForm::begin([
            'id' => 'contact-list-ajax',
            'action' => ['/contacts/list-ajax'],
            'method' => 'get',
        ]);

        echo Html::input('text', 'q', null, ['class' => 'contacts__search-input', 'placeholder' => 'Search Contacts', 'autocomplete' => 'off']);

        ActiveForm::end()

        ?>

    </div>

    <?php

    $js = <<<JS

function showSearchContactPreloader() {
    $("#list-of-contacts").html('<div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div>');
}
function hideSearchContactPreloader() {
    $("#list-of-contacts").html("");
}

$('#contact-list-ajax').on('beforeSubmit', function (e) {
    e.preventDefault();
    let yiiform = $(this);
    let q = yiiform.find("input[name=q]").val();
    if (q.length < 2) {
         new PNotify({
            title: "Search contacts",
            type: "warning",
            text: 'Minimum 2 symbols',
            hide: true
        });
        return false;
    }
    showSearchContactPreloader();
    $.ajax({
            type: yiiform.attr('method'),
            url: yiiform.attr('action'),
            data: yiiform.serializeArray(),
            dataType: 'json',
        }
    )
    .done(function(data) {
        hideSearchContactPreloader();
        if (data.results.length < 1) {
            $("#list-of-contacts").html('<div style="width:100%;text-align:center;margin-top:20px">Not found</div>');
            return;
        }
        $.each(data.results, function(i, item) {
            if (!i || !item) {
                return;
            }
            let content = '<span class="section-separator">' + i + '</span>'; 
            content += '<ul class="phone-widget__list-item calls-history" id="contacts-tab">';
            $.each(data.results[i], function(j, contact) {
                content += getContactItem(contact);
            });            
            content += '</ul>';
            $("#list-of-contacts").append(content);
        });
    })
    .fail(function () {
        hideSearchContactPreloader();
        new PNotify({
            title: "Search contacts",
            type: "error",
            text: 'Server Error. Try again later',
            hide: true
        });
    })
    return false;
})

function getContactItem(contact) {
    let content = '<li class="calls-history__item contact-info-card is-collapsible">' +
            '<div class="collapsible-toggler collapsed" data-toggle="collapse" data-target="#collapse' + contact['id'] + '" aria-expanded="false" aria-controls="collapse' + contact['id'] + '">' +
                    '<div class="contact-info-card__status">' +
                    '<div class="agent-text-avatar">' +
                        '<span>' + contact['avatar'] + '</span>' +
                    '</div>' +
                '</div>' +
                '<div class="contact-info-card__details">' +
                    '<div class="contact-info-card__line history-details">' +
                        '<strong class="contact-info-card__name">' + contact['name'] + '</strong>' +
                    '</div>' +
                    '<div class="contact-info-card__line history-details">' +
                        '<span class="contact-info-card__call-type">' + contact['description'] + '</span>' +
                    '</div>' +
                    '<a href="#" class="collapsible-arrow"><i class="fas fa-chevron-right"></i></a>' +
                '</div>' +
            '</div>' +
            '<div id="collapse' + contact['id'] + '" class="collapse collapsible-container" aria-labelledby="headingOne" data-parent="#contacts-tab">' +
                '<ul class="contact-options-list">' +
                    '<li class="contact-options-list__option js-toggle-contact-info" data-contact="' + encodeContact(contact) + '">' +
                        '<i class="fa fa-user"></i>' +
                        '<span>View</span>' +
                    '</li>' +
                '</ul>' +
                '<ul class="contact-full-info">';
    if (contact['phones']) {
        contact['phones'].forEach(function(phone, index) {
            content += '<li>' +
                        '<div class="form-group">' +
                            '<label for="">Phone ' + (index + 1) + '</label>' +
                            '<input readonly type="text" class="form-control" value="' + phone + '" autocomplete="off">' +
                        '</div>' +
                        '<ul class="actions-list">' +
                            '<li class="actions-list__option actions-list__option--phone js-call-tab-trigger">' +
                                '<i class="fa fa-phone phone-dial" data-phone="' + phone + '"></i>' +
                            '</li>' +
                            '<li class="actions-list__option js-trigger-messages-modal">' +
                                '<i class="fa fa-comment-alt"></i>' +
                            '</li>' +
                        '</ul>' +
                    '</li>'; 
        })
    }
    if (contact['emails']) {
        contact['emails'].forEach(function(email, index) {
            content += '<li>' +
                        '<div class="form-group">' +
                            '<label for="">Email ' + (index + 1) + '</label>' +
                            '<input readonly type="email" class="form-control" value="' + email + '" autocomplete="off">' +
                        '</div>' +
                        '<ul class="actions-list">' +
                            '<li class="actions-list__option js-trigger-email-modal">' +
                                '<i class="fa fa-envelope"></i>' +
                            '</li>' +
                        '</ul>' +
                    '</li>'; 
        })
    }
    content += '</ul>' +
            '</div>' +
        '</li>';
    return content;
}

 $(document).on('click', '.phone-dial', function(e) {
  $(".widget-phone__contact-info-modal").hide();
  $('#call-pane__dial-number').val($(this).data('phone'));
  $('.phone-widget__header-actions a[data-toggle-tab="tab-contacts"]').removeClass('is_active');
  $('#tab-contacts').removeClass('is_active');
  $('.phone-widget__header-actions a[data-toggle-tab="tab-phone"]').addClass('is_active');
  $('#tab-phone').addClass('is_active');
 });

function encodeContact(contact) {
    return btoa(JSON.stringify(contact));
}
function decodeContact(contact) {
   return JSON.parse(atob(contact));
}
$(document).on('click', ".js-toggle-contact-info", function () {
    let contact = decodeContact($(this).data('contact'));
    let data = viewContact(contact);
    $(".widget-phone__contact-info-modal").html(data);
    $(".widget-phone__contact-info-modal").show();
});

function viewContact(contact) {
    let content = '<div class="widget-phone__contact-info-modal widget-modal contact-modal-info">' +
            '<a href="#" class="widget-modal__close">' +
                '<i class="fa fa-arrow-left"></i>' +
                'Back to contacts</i>' +
            '</a>' +
            '<div class="contact-modal-info__user">' +
                '<div class="agent-text-avatar">' +
                    '<span>' + contact['avatar'] + '</span>' +
                '</div>' +
                '<h3 class="contact-modal-info__name">' + contact['name'] + '</h3>' +
                '<div class="contact-modal-info__actions">' +
                    '<ul class="contact-options-list">' +
                        '<li class="contact-options-list__option js-edit-mode">' +
                            '<i class="fa fa-user"></i>' +
                            '<span>EDIT</span>' +
                        '</li>' +
                        '<li class="contact-options-list__option js-trigger-messages-modal">' +
                            '<i class="fa fa-comment-alt"></i>' +
                            '<span>SMS</span>' +
                        '</li>' +
                        '<li class="contact-options-list__option contact-options-list__option--call js-call-tab-trigger">' +
                            '<i class="fa fa-phone"></i>' +
                            '<span>Call</span>' +
                        '</li>' +
                    '</ul>' +
                '</div>' +
            '</div>' +
            '<div class="contact-modal-info__body">' +
                '<ul class="contact-modal-info__contacts contact-full-info">' +
                    '<li>' +
                        '<div class="form-group">' +
                            '<label for="">Type</label>' +
                            '<div class="form-control-wrap" data-type="company">' +
                                '<i class="fa fa-building contact-type-company"></i>' +
                                '<i class="fa fa-user contact-type-person"></i>' +
                                '<select readonly type="text" class="form-control select-contact-type" value="Company" autocomplete="off" readonly disabled>';
    if (contact['is_company']) {
        content += '<option value="company" selected="selected">Company</option> <option value="person">Person</option>';
    } else {
        content += '<option value="company">Company</option> <option value="person"  selected="selected">Person</option>';
    }
                                content += '</select>' +
                            '</div>' +
                        '</div>' +
                    '</li>';
    if (contact['phones']) {
        contact['phones'].forEach(function(phone, index) {
            content += '<li>' +
                        '<div class="form-group">' +
                            '<label for="">Phone ' + (index + 1) + '</label>' +
                            '<input readonly type="text" class="form-control" value="' + phone + '" autocomplete="off">' +
                        '</div>' +
                        '<ul class="actions-list">' +
                            '<li class="actions-list__option actions-list__option--phone js-call-tab-trigger">' +
                                '<i class="fa fa-phone phone-dial" data-phone="' + phone + '"></i>' +
                            '</li>' +
                            '<li class="actions-list__option js-trigger-messages-modal">' +
                                '<i class="fa fa-comment-alt"></i>' +
                            '</li>' +
                        '</ul>' +
                    '</li>'; 
        })
    }
    if (contact['emails']) {
        contact['emails'].forEach(function(email, index) {
            content += '<li>' +
                        '<div class="form-group">' +
                            '<label for="">Email ' + (index + 1) + '</label>' +
                            '<input readonly type="email" class="form-control" value="' + email + '" autocomplete="off">' +
                        '</div>' +
                        '<ul class="actions-list">' +
                            '<li class="actions-list__option js-trigger-email-modal">' +
                                '<i class="fa fa-envelope"></i>' +
                            '</li>' +
                        '</ul>' +
                    '</li>'; 
        })
    }
                    
                    content += '</ul>' +
               // '<a href="#" class="contact-modal-info__remove-contact">DELETE CONTACT</a>' +
            '</div>' +
        '</div>';
    return content;
}
JS;

    $this->registerJs($js);
    ?>

    <div id="list-of-contacts"></div>

</div>
