<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadBusinessExtraQueue\entity\LeadBusinessExtraQueue */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-business-extra-queue-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'lbeq_lead_id')->textInput() ?>

    <?= $form->field($model, 'lbeq_lbeqr_id')->textInput() ?>

    <?= $form->field($model, 'lbeq_expiration_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
