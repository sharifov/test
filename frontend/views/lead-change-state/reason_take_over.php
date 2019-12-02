<?php

use sales\forms\leadflow\TakeOverReasonForm;
use yii\widgets\ActiveForm;
use yii\bootstrap\Html;

/**
 * @var $reasonForm TakeOverReasonForm
 */

$form = ActiveForm::begin([
    'id' => $reasonForm->formName(),
    'action' => ['lead-change-state/take-over', 'gid' => $reasonForm->leadGid],
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'validationUrl' => ['lead-change-state/validate-take-over', 'gid' => $reasonForm->leadGid]
]) ?>

<?php //= $form->errorSummary($reasonForm) ?>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($reasonForm, 'reason', [
            ])->dropDownList(TakeOverReasonForm::REASON_LIST, [
                'prompt' => 'Select reason',
                'onchange' => "
                var val = $(this).val();
                if (val == '" . TakeOverReasonForm::REASON_OTHER . "') {
                    $('#" . $form->id . "-other-wrapper').addClass('show');
                    $('#" . $form->id . "-duplicate-wrapper').removeClass('show');
                } else if (val == '" . TakeOverReasonForm::REASON_DUPLICATE . "') {
                    $('#" . $form->id . "-duplicate-wrapper').addClass('show');
                    $('#" . $form->id . "-other-wrapper').removeClass('show');
                } else {
                    $('#" . $form->id . "-other-wrapper').removeClass('show');
                    $('#" . $form->id . "-duplicate-wrapper').removeClass('show');
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
