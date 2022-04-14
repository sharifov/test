<?php

use frontend\widgets\multipleUpdate\cases\MultipleUpdateForm;
use src\widgets\DateTimePicker;
use common\components\bootstrap4\activeForm\ActiveForm;
use yii\bootstrap4\Html;
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

    <?= $form->field($updateForm, 'statusId')->dropDownList($updateForm->statusList(), ['prompt' => 'Select status']) ?>

    <div class="reason-wrapper d-none">

        <?= $form->field($updateForm, 'reason')->dropDownList([]) ?>

        <div class="message-wrapper d-none">
            <?= $form->field($updateForm, 'message')->textarea() ?>
        </div>

    </div>

    <div class="deadline-wrapper d-none">
        <?= $form->field($updateForm, 'deadline')->widget(DateTimePicker::class) ?>
    </div>

    <div class="user-wrapper d-none">
        <?= $form->field($updateForm, 'userId')->dropDownList($updateForm->userList(), ['prompt' => 'Select employee']) ?>
    </div>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fa fa-check-square"></i> ' . $buttonHeader, ['class' => 'btn btn-info']) ?>
    </div>

<?php
ActiveForm::end();

$statusId = Html::getInputId($updateForm, 'statusId');
$reasonId = Html::getInputId($updateForm, 'reason');
$messageId = Html::getInputId($updateForm, 'message');
$userId = Html::getInputId($updateForm, 'userId');
$deadlineId = Html::getInputId($updateForm, 'deadline');
$reasons = $updateForm->reasonList();

$js = <<<JS

var reason = $('#{$reasonId}'); 
var reasons = {$reasons};
var message = $('#{$messageId}');
var user = $('#{$userId}');
var reasonWrapper = $('.reason-wrapper');
var messageWrapper = $('.message-wrapper');
var userWrapper = $('.user-wrapper');
var deadlineWrapper = $('.deadline-wrapper');
reason.parent().addClass('required');
message.parent().addClass('required');
user.parent().addClass('required');
    
$('body').find('#{$statusId}').on('change', function () {
    var val = $(this).val() || null;
    resetMultiUpdateForm();
    if (val in reasons) {
         reason.append('<option value="">Select reason</select>');
         $.each(reasons[val], function (i, elem) {
             reason.append('<option value="'+i+'">' + elem +'</select>');
         });
         reasonWrapper.removeClass('d-none');
    }
    if (val == '{$updateForm->statusProcessingId()}') {
        userWrapper.removeClass('d-none');
    }
    if (val == '{$updateForm->statusFollowUpId()}') {
        deadlineWrapper.removeClass('d-none');
    } else {
        deadlineWrapper.addClass('d-none');
    }
})

$('body').find('#{$reasonId}').on('change', function () {
    var val = $(this).val() || null;
    if (val == '{$updateForm->reasonOther()}') {
        message.val('');
        messageWrapper.removeClass('d-none');
    } else {
        messageWrapper.addClass('d-none');
    }
});

function resetMultiUpdateForm() {
     reason.html('');
     message.val('');
     reasonWrapper.addClass('d-none');
     messageWrapper.addClass('d-none');
     userWrapper.addClass('d-none');
     deadlineWrapper.addClass('d-none');
     user[0].selectedIndex = 0;
}

$('body').find('#{$formId}').on('beforeSubmit', function (e) {
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
JS;
$this->registerJs($js);
