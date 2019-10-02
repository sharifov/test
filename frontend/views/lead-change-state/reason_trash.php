<?php

use sales\forms\leadflow\TrashReasonForm;
use yii\widgets\ActiveForm;
use yii\bootstrap\Html;

/**
 * @var $reasonForm TrashReasonForm
 */

$form = ActiveForm::begin([
    'id' => $reasonForm->formName(),
    'action' => ['lead-change-state/trash', 'gid' => $reasonForm->leadGid],
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'validationUrl' => ['lead-change-state/validate-trash', 'gid' => $reasonForm->leadGid]
]) ?>

<?php //= $form->errorSummary($reasonForm) ?>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($reasonForm, 'reason', [
            ])->dropDownList(TrashReasonForm::REASON_LIST, [
                'prompt' => 'Select reason',
                'onchange' => "
                var val = $(this).val();
                if (val == '" . TrashReasonForm::REASON_OTHER . "') {
                    $('#" . $form->id . "-other-wrapper').addClass('in');
                    $('#" . $form->id . "-duplicate-wrapper').removeClass('in');
                } else if (val == '" . TrashReasonForm::REASON_DUPLICATE . "') {
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
