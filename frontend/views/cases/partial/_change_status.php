<?php

use sales\widgets\DateTimePicker;
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

        <div class="deadline-wrapper d-none">
            <?= $form->field($statusForm, 'deadline')->widget(DateTimePicker::class) ?>
        </div>

        <?php if ($statusForm->isEnabledSendFeedbackEmail()) : ?>
            <?php if ($statusForm->isResendFeedbackEnable()) : ?>
                <div class="resend-wrapper d-none">
                    <?= Html::checkbox(Html::getInputName($statusForm, 'resendFeedbackForm'), $statusForm->resendFeedbackForm, [
                        'id' => Html::getInputId($statusForm, 'resendFeedbackForm'),
                        'label' => 'Resend feedback form',
                    ])?>
                </div>
            <?php else : ?>
                <div class="resend-wrapper d-none">
                    <?= Html::checkbox(Html::getInputName($statusForm, 'resendFeedbackForm'), $statusForm->resendFeedbackForm, [
                        'id' => Html::getInputId($statusForm, 'resendFeedbackForm'),
                        'label' => 'Send feedback form',
                        'disabled' => 'disabled',
                    ])?>
                </div>
            <?php endif; ?>

            <div class="sendto-wrapper d-none">
                <?= $form->field($statusForm, 'sendTo')->dropDownList($statusForm->getSendToList()) ?>
            </div>

            <div class="language-wrapper d-none">
                <?= $form->field($statusForm, 'language')->dropDownList($statusForm->getLanguageList(), [
                        'prompt' => '---',
                        'options' => [
                            $statusForm->getLanguageDefault() => ['selected' => true]
                        ],
                ]) ?>
            </div>
        <?php endif;?>

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
$deadlineId = Html::getInputId($statusForm, 'deadline');
$sendToId = Html::getInputId($statusForm, 'sendTo');
$languageId = Html::getInputId($statusForm, 'language');
$resendId = Html::getInputId($statusForm, 'resendFeedbackForm');
$reasons = $statusForm->reasons();

$resendRequire = $statusForm->resendFeedbackForm ? 1 : 0;

$js = <<<JS

var reason = $('#{$reasonId}'); 
var reasons = {$reasons};
var message = $('#{$messageId}');
var user = $('#{$userId}');
var deadline = $('#{$deadlineId}');
var language = $('#{$languageId}');
var sendTo = $('#{$sendToId}');
var resend = $('#{$resendId}');
var reasonWrapper = $('.reason-wrapper');
var messageWrapper = $('.message-wrapper');
var userWrapper = $('.user-wrapper');
var deadlineWrapper = $('.deadline-wrapper');
var sendToWrapper = $('.sendto-wrapper');
var languageWrapper = $('.language-wrapper');
var resendWrapper = $('.resend-wrapper');

reason.parent().addClass('required');
message.parent().addClass('required');
user.parent().addClass('required');

if ({$resendRequire}) {
    feedbackSendBlockRequired();
}

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
    }
    if (val == '{$statusForm->statusProcessingId()}') {
        userWrapper.removeClass('d-none');
    }
    if (val == '{$statusForm->statusFollowUpId()}') {
        deadlineWrapper.removeClass('d-none');
    } else {
        deadlineWrapper.addClass('d-none');
    }
    if (val == '{$statusForm->statusSolvedId()}') {
        sendToWrapper.removeClass('d-none');
        languageWrapper.removeClass('d-none');
        resendWrapper.removeClass('d-none');
        if ({$resendRequire}) {
            feedbackSendBlockRequired();
        } else {
            feedbackSendBlockUnRequired();
        }
    } else {
        sendToWrapper.addClass('d-none');
        languageWrapper.addClass('d-none');
        resendWrapper.addClass('d-none');
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

message.on('input',function() {
    removeStatusFormErrors();
});

deadline.on('change',function() {
    removeStatusFormErrors();
});

language.on('change',function() {
    removeStatusFormErrors();
});

sendTo.on('change',function() {
    removeStatusFormErrors();
});

resend.on('change', function () {
    if (this.checked) {
        feedbackSendBlockRequired();
    } else {
        feedbackSendBlockUnRequired();
    }
    removeStatusFormErrors();
});

function feedbackSendBlockRequired() {
    sendTo.parent().addClass('required');
    language.parent().addClass('required');
}
function feedbackSendBlockUnRequired() {
    sendTo.parent().removeClass('required');
    language.parent().removeClass('required');
}

function removeStatusFormErrors() {
    let form = $("#{$formId}");
    form.find('.alert.alert-danger').hide();
    form.find('.is-invalid').each(function (index, el) {
        $(el).removeClass('is-invalid');
    });
    form.find('.invalid-feedback').html('');
}

function resetStatusForm() {
    $("#{$formId}").get(0).reset();
    reason.html('');
    reasonWrapper.addClass('d-none');
    messageWrapper.addClass('d-none');
    userWrapper.addClass('d-none');
    deadlineWrapper.addClass('d-none');
    sendToWrapper.addClass('d-none');
    languageWrapper.addClass('d-none');
    resendWrapper.addClass('d-none');
}

JS;
$this->registerJs($js);
