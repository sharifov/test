<?php

use sales\access\ListsAccess;
use sales\forms\leadflow\ReturnReasonForm;
use yii\widgets\ActiveForm;
use yii\bootstrap\Html;

/**
 * @var $reasonForm ReturnReasonForm
 */

$form = ActiveForm::begin([
    'id' => $reasonForm->formName(),
    'action' => ['lead-change-state/return', 'gid' => $reasonForm->leadGid],
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'validationUrl' => ['lead-change-state/validate-return', 'gid' => $reasonForm->leadGid]
]) ?>

<?php //= $form->errorSummary($reasonForm) ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($reasonForm, 'reason', [
            ])->dropDownList(ReturnReasonForm::REASON_LIST, [
                'prompt' => 'Select reason',
                'onchange' => "
                var val = $(this).val();
                if (val == '" . ReturnReasonForm::REASON_OTHER . "') {
                    $('#" . $form->id . "-other-wrapper').addClass('in');
                } else {
                    $('#" . $form->id . "-other-wrapper').removeClass('in');
                }
            "]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($reasonForm, 'return', [
            ])->dropDownList(ReturnReasonForm::RETURN_LIST, [
                'prompt' => 'Select return in',
                'onchange' => "
                var val = $(this).val();
                if (val == '" . ReturnReasonForm::RETURN_PROCESSING . "') {
                    $('#" . $form->id . "-user-wrapper').addClass('in');
                } else {
                    $('#" . $form->id . "-user-wrapper').removeClass('in');
                }
            "]) ?>
        </div>
    </div>

    <div class="form-group collapse" id="<?= $form->id ?>-user-wrapper">
        <?= $form->field($reasonForm, 'userId')->dropDownList((new ListsAccess())->getEmployees(), ['prompt' => 'Select Agent'])->label('Agent') ?>
    </div>

    <div class="form-group collapse" id="<?= $form->id ?>-other-wrapper">
        <?= $form->field($reasonForm, 'other')->textarea(['rows' => 5]) ?>
    </div>

    <div class="actions-btn-wrapper">
        <?= Html::submitButton('Add', ['class' => 'btn btn-success popover-close-btn']) ?>
    </div>

<?php ActiveForm::end();
