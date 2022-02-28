<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadStatusReasonLog\entity\LeadStatusReasonLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-status-reason-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'lsrl_id') ?>

    <?= $form->field($model, 'lsrl_lead_flow_id') ?>

    <?= $form->field($model, 'lsrl_lead_status_reason_id') ?>

    <?= $form->field($model, 'lsrl_comment') ?>

    <?= $form->field($model, 'lsrl_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
