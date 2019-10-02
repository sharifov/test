<?php

use sales\forms\leadflow\RejectReasonForm;
use yii\widgets\ActiveForm;
use yii\bootstrap\Html;

/**
 * @var $reasonForm RejectReasonForm
 */

$form = ActiveForm::begin([
    'id' => $reasonForm->formName(),
    'action' => ['lead-change-state/reject', 'gid' => $reasonForm->leadGid],
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'validationUrl' => ['lead-change-state/validate-reject', 'gid' => $reasonForm->leadGid]
]) ?>

<?php //= $form->errorSummary($reasonForm) ?>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($reasonForm, 'reason', [
            ])->dropDownList(RejectReasonForm::REASON_LIST, [
                'prompt' => 'Select reason',
                'onchange' => "
                var val = $(this).val();
                if (val == '" . RejectReasonForm::REASON_OTHER . "') {
                    $('#" . $form->id . "-other-wrapper').addClass('in');
                    $('#" . $form->id . "-duplicate-wrapper').removeClass('in');
                } else if (val == '" . RejectReasonForm::REASON_DUPLICATE . "') {
                    $('#" . $form->id . "-duplicate-wrapper').addClass('in');
                    $('#" . $form->id . "-other-wrapper').removeClass('in');
                } else {
                    $('#" . $form->id . "-other-wrapper').removeClass('in');
                    $('#" . $form->id . "-duplicate-wrapper').removeClass('in');
                }
            "]) ?>
        </div>
    </div>

    <div class="form-group collapse" id="<?= $form->id ?>-duplicate-wrapper">
        <?= $form->field($reasonForm, 'originId')->textInput() ?>
    </div>

    <div class="form-group collapse" id="<?= $form->id ?>-other-wrapper">
        <?= $form->field($reasonForm, 'other')->textarea(['rows' => 5]) ?>
    </div>

    <div class="actions-btn-wrapper">
        <?= Html::submitButton('Add', ['class' => 'btn btn-success popover-close-btn']) ?>
    </div>

<?php ActiveForm::end();
