<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadStatusReasonLog\entity\LeadStatusReasonLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-status-reason-log-form">

    <div class="col-md-2">
        <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'lsrl_lead_flow_id')->textInput() ?>

            <?= $form->field($model, 'lsrl_lead_status_reason_id')->textInput() ?>

            <?= $form->field($model, 'lsrl_comment')->textInput(['maxlength' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
