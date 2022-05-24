<?php

use frontend\widgets\multipleUpdate\lead\MultipleUpdateForm;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/** @var  $updateForm MultipleUpdateForm */
/** @var  $validationUrl string */
/** @var  $action string */
/** @var  $modalId string */
/** @var  $pjaxId string */
/** @var  $script string */
/** @var  $formId string */
/** @var  $buttonHeader string */
/** @var  $notifyHeader string */
/** @var  $summaryIdentifier string */
/** @var  $this View */

$form = ActiveForm::begin([
    'id' => $formId,
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    'validationUrl' => $validationUrl,
    'action' => $action,
]);

?>

    <?= $form->errorSummary($updateForm) ?>

    <?= $form->field($updateForm, 'ids', [
        'template' => '{input}',
        'options' => ['tag' => false]
    ])->hiddenInput()->label(false) ?>

    <div class="redial_queue-wrapper">

        <?= $form->field($updateForm, 'statusId')->dropDownList($updateForm->statusList(), ['prompt' => 'Select status']) ?>

        <div class="reason-wrapper d-none">

            <?= $form->field($updateForm, 'reason')->dropDownList([]) ?>

            <div class="alert alert-info" style="display: none;" id="lead-close-reason-description"></div>

            <div class="message-wrapper d-none">
                <?= $form->field($updateForm, 'message')->textarea() ?>
            </div>

        </div>

        <?= $form->field($updateForm, 'userId')->widget(Select2::class, [
            'data' => $updateForm->userList(),
            'size' => Select2::SMALL,
            'options' => ['placeholder' => 'Select employee']
        ]) ?>

    </div>

    <?php if ($updateForm->authUserIsAdmin()) : ?>
        <?= $form->field($updateForm, 'redial_queue')->dropDownList($updateForm->getRedialQueueList(), [
            'prompt' => '',
            'onChange' => 'let wrapper = $(this).val(); if (wrapper == 1 || wrapper == 2) $(".redial_queue-wrapper").hide(); else $(".redial_queue-wrapper").show();'
        ]) ?>
    <?php endif; ?>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fa fa-check-square"></i> ' . $buttonHeader, ['class' => 'btn btn-info']) ?>
    </div>

<?php
ActiveForm::end();

$statusId = Html::getInputId($updateForm, 'statusId');
$reasonId = Html::getInputId($updateForm, 'reason');
$messageId = Html::getInputId($updateForm, 'message');
$userId = Html::getInputId($updateForm, 'userId');
$reasonList = Json::encode($updateForm->reasonList());

$url = Url::to(['/lead-change-state/ajax-changed-close-reason', 'multipleUpdate' => true]);
$js = <<<JS

var reason = $('#{$reasonId}'); 
var reasonList = {$reasonList};
var message = $('#{$messageId}');
var user = $('#{$userId}');
var reasonWrapper = $('.reason-wrapper');
var messageWrapper = $('.message-wrapper');

message.parent().addClass('required');
    
$('#{$statusId}').on('change', function () {
    var val = $(this).val() || null;
    resetMultiUpdateForm();
    if (val in reasonList) {
         reason.append('<option value="">Select reason</select>');
         $.each(reasonList[val], function (i, elem) {
             reason.append('<option value="'+i+'">' + elem +'</select>');
         });
         reasonWrapper.removeClass('d-none');
    }
})

$('#{$reasonId}').on('change', function () {
    var val = $(this).val() || null;
    var selectedStatus = $('#{$statusId}').val();
    if (val == '{$updateForm->reasonOther()}') {
        message.val('');
        messageWrapper.removeClass('d-none');
    } else if (selectedStatus == '{$updateForm->getClosedStatusId()}') {
        getReasonData(val);
    } else {
        messageWrapper.addClass('d-none');
    }
});

function resetMultiUpdateForm() {
     reason.html('');
     message.val('');
     reasonWrapper.addClass('d-none');
     messageWrapper.addClass('d-none');
     user[0].selectedIndex = 0;
     $('#lead-close-reason-description').hide();
}

$('#{$formId}').on('beforeSubmit', function (e) {
    e.preventDefault();
    $.ajax({
       type: $(this).attr('method'),
       url: $(this).attr('action'),
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(data) {
            $('#{$modalId}').modal('toggle');
            var message = '';
            if (data.success) {
                message = 'Success';
                if (data.message) {
                    message = data.message;
                }
                createNotifyByObject({title: '{$notifyHeader}', text: message, type: 'info'});
            } else {
                message = 'Error. Try again later.';
                if (data.message) {
                    message = data.message;
                }
                createNotifyByObject({title: '{$notifyHeader}', text: message, type: 'error'});
            }
            var summary = '{$summaryIdentifier}';
            if (summary && data.text) {
                $('body').find('{$summaryIdentifier}').html(data.text);
            }
            var pjaxId = '{$pjaxId}';
            if (pjaxId) {
                var pjax = $('#' + pjaxId); 
                if (pjax.length) {
                    $.pjax.reload({container: ('#' + pjaxId), async: false}); 
                }
            }
            {$script}
       },
       error: function (error) {
           $('#{$modalId}').modal('toggle');
           createNotifyByObject({title: 'Error', text: 'Internal Server Error. Try again later.', type: 'error'});
       }
    })
    return false;
});

function getReasonData(reasonKey)
{
    $.ajax({
        type: 'get',
        url: '$url' + '&reasonKey=' + reasonKey,
        cache: false,
        dataType: 'json',
        beforeSend: function () {
            $('#close-reason-submit').addClass('disabled').prop('disabled', true);           
        },
        success: function (data) {
            $('#lead-close-reason-description').hide();
            if (data.description) {
                $('#lead-close-reason-description').html(data.description);
                $('#lead-close-reason-description').show();
            }
            
            if (data.commentRequired) {
                messageWrapper.removeClass('d-none');
            } else {
                messageWrapper.addClass('d-none');
            }
        },
        complete: function () {
            $('#close-reason-submit').removeClass('disabled').prop('disabled', false);                                     
        },
        error: function (xhr) {
            createNotify('Error', xhr.responseText, 'error');
        }
    });
}
JS;
$this->registerJs($js);
