<?php

use sales\forms\lead\CloneReasonForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var CloneReasonForm $reasonForm */

$form = ActiveForm::begin([
    'id' => $reasonForm->formName(),
    'action' => ['lead/clone', 'id' => $reasonForm->leadId],
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'validationUrl' => ['lead/clone', 'id' => $reasonForm->leadId]
]) ?>

<?php //= $form->errorSummary($reasonForm) ?>

<div class="row">
    <div class="col-sm-12">
        <?= $form->field($reasonForm, 'reason', [
        ])->dropDownList(CloneReasonForm::REASON_LIST, [
            'prompt' => 'Select reason',
            'onchange' => "
                var val = $(this).val();
                if (val == '" . CloneReasonForm::REASON_OTHER . "') {
                    $('#" . $form->id . "-other-wrapper').addClass('show');
                } else {
                    $('#" . $form->id . "-other-wrapper').removeClass('show');
                }
            "]) ?>
    </div>
</div>

<div class="form-group collapse" id="<?= $form->id ?>-other-wrapper">
    <?= $form->field($reasonForm, 'other')->textarea(['rows' => 5]) ?>
</div>

<div class="btn-wrapper">
    <?= Html::button('<i class="glyphicon glyphicon-remove-circle"></i> Cancel', ['id' => 'cancel-btn', 'class' => 'btn btn-danger', 'data-dismiss' => 'modal']) ?>
    <?= Html::submitButton('<i class="fa fa-save"></i> Confirm', ['id' => 'save-btn', 'class' => 'btn btn-primary']) ?>
</div>

<?php

ActiveForm::end();
