<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pr_type_id')->textInput() ?>

    <?= $form->field($model, 'pr_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pr_lead_id')->textInput() ?>

    <?= $form->field($model, 'pr_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pr_status_id')->textInput() ?>

    <?= $form->field($model, 'pr_service_fee_percent')->textInput(['maxlength' => true]) ?>

    <?//= $form->field($model, 'pr_created_user_id')->textInput() ?>

    <?//= $form->field($model, 'pr_updated_user_id')->textInput() ?>

    <?//= $form->field($model, 'pr_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
