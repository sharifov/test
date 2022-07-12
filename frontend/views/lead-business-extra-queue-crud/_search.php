<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadBusinessExtraQueue\entity\LeadBusinessExtraQueueSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-business-extra-queue-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'lbeq_lead_id') ?>

    <?= $form->field($model, 'lbeq_lbeqr_id') ?>

    <?= $form->field($model, 'lbeq_created_dt') ?>

    <?= $form->field($model, 'lbeq_expiration_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
