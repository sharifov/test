<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-business-extra-queue-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'lbeql_lbeqr_id')->textInput() ?>

    <?= $form->field($model, 'lbeql_lead_id')->textInput() ?>

    <?= $form->field($model, 'lbeql_status')->textInput() ?>

    <?= $form->field($model, 'lbeql_created_dt')->textInput() ?>

    <?= $form->field($model, 'lbeql_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
