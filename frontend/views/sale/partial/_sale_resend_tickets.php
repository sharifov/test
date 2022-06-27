<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $caseSale common\models\CaseSale */
/* @var $caseSaleData array */
/* @var $caseSaleEmail string */

$caseSaleData = $caseSale->css_sale_data_updated;
$caseSaleEmail = $caseSaleData['email'];
$caseSaleId = $caseSale->css_sale_id;
$caseId = $caseSale->css_cs_id;
$urlResendTickets = Url::to(['/sale/resend-tickets']);
?>

<div class="row">
    <div class="col-md-12">
        <h6 class="bold">Send Ticket Receipts To:</h6>
    </div>
</div>
<div class="row">
    <div class="col-md-6 col-sm-6">
        <div class="form-group">
            <div class="modal-checkboxes-control">
                <a class="btn btn-warning btn-sm checked" id="check-all" href="javascript:void(0)">
                    <i class="fa fa-check-square-o"></i>
                    Uncheck All (1)
                </a>
            </div>

            <div id="email-list">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="emails[]" value="<?= $caseSaleEmail ?>" checked>
                        <b><?= $caseSaleEmail ?></b>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6">
        <div class="form-inline">
            <input type="text" class="form-control" name="add_email" value="" placeholder="Add Email">
            <button type="button" id="add-email-btn" class="btn btn-success" disabled style="margin-bottom: 0">
                <i class="fa fa-plus"></i>
            </button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 text-center">
        <button type="button" class="btn btn-success resend-tickets-btn" data-case-sale-id="<?= $caseSaleId ?>" data-case-id="<?= $caseId ?>">
            Resend Tickets
        </button>
    </div>
</div>

<?php
$js = <<<JS
    $('.resend-tickets-btn').on('click', function (e) {
        e.preventDefault();
        
        let btn = $(this);
        let modal = btn.parents('.modal');
        let btnClass = btn.find('i').attr('class');
        let caseId = btn.data('case-id');
        let caseSaleId = btn.data('case-sale-id');
        let emails = [];
        let loader = $('#preloader');
        $.each($('#email-list input[type="checkbox"]:checked'), function() {
            emails.push($(this).val());
        });
        
        if(emails.length === 0){
            alert('Please select at least one email address');
            return;
        }
        
        $.ajax({
            url: "$urlResendTickets",
            type: 'post',
            data: {
                caseId: caseId,
                caseSaleId: caseSaleId,
                emails: emails
            },
            beforeSend: function () {
                btn.attr('disabled', true).addClass('disabled').find('i').attr('class', 'fas fa-spinner fa-spin');
                loader.removeClass('d-none');
            },
            success: function (data) {
                if (data.error) {
                    createNotifyByObject({
                        title: "Error",
                        type: "error",
                        text: data.message,
                        hide: true
                    }); 
                } else {
                    createNotifyByObject({
                        title: "Success",
                        type: "success",
                        text: data.message,
                        hide: true
                    });
                    modal.find('.close').trigger('click');
                }
            },
            error: function (error) {
                console.log('Error: ' + error);
            },
            complete: function () {
                btn.removeClass('disabled').find('i').attr('class', btnClass);
                loader.addClass('d-none');
            }
        });
    });
    
    $('#check-all').on('click', function(e) {
        e.preventDefault();
        
        let btn = $(this);
        let checkboxes = $("#email-list input[name='emails[]']");
        if (btn.hasClass('checked')) {
            checkboxes.prop('checked', false);
            btn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<i class="fa fa-square-o"></i> Check All');
        } else {
            checkboxes.prop('checked', true);
            btn.removeClass('btn-default').addClass(['btn-warning', 'checked']).html('<i class="fa fa-check-square-o"></i> Uncheck All (' + checkboxes.length + ')');
        }
    });
    
    $("input[name='add_email']").on('keyup', function (event) {
         if (event.which === 8 || event.which === 46) {
             if ($(this).val().length === 0) {
                $('#add-email-btn').attr('disabled', true);
             } else {
                 $('#add-email-btn').attr('disabled', !isEmail($(this).val()));
             }
         } else {
             $('#add-email-btn').attr('disabled', !isEmail($(this).val()));
         }
    });
    
    $('body').on('change', '#email-list input[type="checkbox"]', function () {
        updatebtn();
    });
    
    $('#add-email-btn').on('click', function() {
        let emails = [];
        let template = '' +
         '<div class="checkbox">' +
         '    <label>' +
         '        <input type="checkbox" name="emails[]" checked value="added_email" data-label="added_email">' +
         '        <b>added_email</b>' +
         '    </label>' +
         '</div>';
        let checkboxes = $('#email-list input[type="checkbox"]');
        $.each(checkboxes, function( index, checkbox ) {
            emails.push($(checkbox).attr('value'));
        });
        let email = $("input[name='add_email']");
        let emailValue = email.val().trim();
        
        if (jQuery.inArray(emailValue, emails) === -1) {
            var addEmail = template.replace(/added_email/g, emailValue);
            $('#email-list').append(addEmail);
        }
        email.val('');
        $('#add-email-btn').attr('disabled', true);
        
        updatebtn();
    });
    
    function isEmail(email) {
        let regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }
    
    function updatebtn(){
        let checkboxes = $('#email-list input[type="checkbox"]:checked');
        let totalChecked = checkboxes.length;
        let btn = $('#check-all');
        if(totalChecked > 0){
            if (btn.hasClass('checked')) {
                btn.html('<i class="fa fa-check-square-o"></i> Uncheck All (' + totalChecked + ')')
            } else {
                btn.removeClass('btn-default').addClass(['btn-warning', 'checked']).html('<i class="fa fa-check-square-o"></i> Uncheck All (' + totalChecked + ')');
            }
        } else {
            btn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<i class="fa fa-square-o"></i> Check All');
        }
    }
JS;

$this->registerJs($js);
