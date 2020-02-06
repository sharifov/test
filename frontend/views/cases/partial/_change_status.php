<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $statusForm sales\forms\cases\CasesChangeStatusForm */
/* @var $form yii\widgets\ActiveForm */

$formId = 'change-status-form-id';

?>

    <div class="cases-change-status">

        <?php $form = ActiveForm::begin([
            'id' => $formId,
            'action' => ['cases/change-status', 'gid' => $statusForm->caseGid],
            'validateOnChange' => false,
            'validateOnBlur' => false,
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
        ]); ?>

        <?= $form->errorSummary($statusForm) ?>

        <?= $form->field($statusForm, 'statusId')->dropDownList($statusForm->statusList(), ['prompt' => '-']) ?>

        <div class="reason-wrapper d-none">

            <?= $form->field($statusForm, 'reason')->dropDownList([]) ?>

            <div class="message-wrapper d-none">
                <?= $form->field($statusForm, 'message')->textarea(['rows' => 3]) ?>
            </div>

        </div>

        <div class="user-wrapper d-none">
            <?= $form->field($statusForm, 'userId')->dropDownList($statusForm->userList(), ['prompt' => 'Select employee']) ?>
        </div>

        <div class="form-group text-center">
            <?= Html::submitButton('Change Status', ['class' => 'btn btn-warning']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

<?php

$statusId = Html::getInputId($statusForm, 'statusId');
$reasonId = Html::getInputId($statusForm, 'reason');
$messageId = Html::getInputId($statusForm, 'message');
$userId = Html::getInputId($statusForm, 'userId');
$reasons = $statusForm->reasons();

$js = <<<JS

var reason = $('#{$reasonId}'); 
var reasons = {$reasons};
var message = $('#{$messageId}');
var user = $('#{$userId}');
var reasonWrapper = $('.reason-wrapper');
var messageWrapper = $('.message-wrapper');
var userWrapper = $('.user-wrapper');

reason.parent().addClass('required');
message.parent().addClass('required');
user.parent().addClass('required');
    
$('body').find('#{$statusId}').on('change', function () {
    var val = $(this).val() || null;
    resetStatusForm();
    $(this).val(val);
    if (val in reasons) {
         reason.append('<option value="">Select reason</select>');
         $.each(reasons[val], function (i, elem) {
             reason.append('<option value="'+i+'">' + elem +'</select>');
         });
         reasonWrapper.removeClass('d-none');
         return;
    }
    if (val == '{$statusForm->statusProcessingId()}') {
        userWrapper.removeClass('d-none');
        return;
    }
})

reason.on('change', function () {
    removeStatusFormErrors();
    var val = $(this).val() || null;
    if (val == '{$statusForm->reasonOther()}') {
        message.val('');
        messageWrapper.removeClass('d-none');
    } else {
        messageWrapper.addClass('d-none');
    }
});

user.on('change', function () {
    removeStatusFormErrors();
});

message.on('input',function(e){
    removeStatusFormErrors();
});

function removeStatusFormErrors() {
    $("#{$formId}").find(".alert.alert-danger").hide();
            
    $("#{$statusId}").parent().find('.invalid-feedback').html('');
    $("#{$statusId}").removeClass('is-invalid');
    
    reason.parent().find('.invalid-feedback').html('');
    reason.removeClass('is-invalid');
    
    message.parent().find('.invalid-feedback').html('');
    message.removeClass('is-invalid');
    
    user.parent().find('.invalid-feedback').html('');
    user.removeClass('is-invalid');
}

function resetStatusForm() {
    $("#{$formId}").get(0).reset();
    reason.html('');
    reasonWrapper.addClass('d-none');
    messageWrapper.addClass('d-none');
    userWrapper.addClass('d-none');
}

JS;
$this->registerJs($js);
