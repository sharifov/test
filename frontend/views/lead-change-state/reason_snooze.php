<?php

use sales\forms\leadflow\SnoozeReasonForm;
use yii\widgets\ActiveForm;
use yii\bootstrap\Html;

/**
 * @var $reasonForm SnoozeReasonForm
 */

$form = ActiveForm::begin([
    'id' => $reasonForm->formName(),
    'action' => ['lead-change-state/snooze', 'gid' => $reasonForm->leadGid],
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'validationUrl' => ['lead-change-state/validate-snooze', 'gid' => $reasonForm->leadGid]
]) ?>

<?php //= $form->errorSummary($reasonForm) ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($reasonForm, 'reason', [
            ])->dropDownList(SnoozeReasonForm::REASON_LIST, [
                'prompt' => 'Select reason',
                'onchange' => "
                var val = $(this).val();
                if (val == '" . SnoozeReasonForm::REASON_OTHER . "') {
                    $('#" . $form->id . "-other-wrapper').addClass('show');
                } else {
                    $('#" . $form->id . "-other-wrapper').removeClass('show');
                }
            "]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($reasonForm, 'snoozeFor')
                ->widget(\dosamigos\datetimepicker\DateTimePicker::class, [
                    'clientOptions' => [
                        'autoclose' => true,
                        "todayHighlight" => true,
                        "format" => "yyyy-mm-dd hh:ii",
                        "orientation" => "bottom left",
                        "startDate" => date('Y-m-d H:i:s')
                    ]
                ])
            ?>
        </div>
    </div>

    <div class="form-group collapse" id="<?= $form->id ?>-other-wrapper">
        <?= $form->field($reasonForm, 'other')->textarea(['rows' => 5]) ?>
    </div>

    <div class="actions-btn-wrapper">
        <?= Html::submitButton('Add', ['class' => 'btn btn-success popover-close-btn']) ?>
    </div>

<?php ActiveForm::end();
