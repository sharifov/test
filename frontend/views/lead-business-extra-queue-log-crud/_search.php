<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-business-extra-queue-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'lbeql_id') ?>

    <?= $form->field($model, 'lbeql_lbeqr_id') ?>

    <?= $form->field($model, 'lbeql_lead_id') ?>

    <?= $form->field($model, 'lbeql_status') ?>

    <?= $form->field($model, 'lbeql_lead_owner_id') ?>

    <?php // echo $form->field($model, 'lbeql_created_dt') ?>

    <?php // echo $form->field($model, 'lbeql_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
