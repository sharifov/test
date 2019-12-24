<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\Hotel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hotel-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ph_product_id')->textInput() ?>

    <?= $form->field($model, 'ph_check_in_date')->textInput() ?>

    <?= $form->field($model, 'ph_check_out_date')->textInput() ?>

    <?= $form->field($model, 'ph_destination_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ph_min_star_rate')->textInput() ?>

    <?= $form->field($model, 'ph_max_star_rate')->textInput() ?>

    <?= $form->field($model, 'ph_max_price_rate')->textInput() ?>

    <?= $form->field($model, 'ph_min_price_rate')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
