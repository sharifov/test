<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentMethod */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-method-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
        <?= $form->field($model, 'pm_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pm_short_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pm_category_id')->dropDownList(\common\models\PaymentMethod::getCategoryList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'pm_enabled')->checkbox() ?>

<!--    --><?php //= $form->field($model, 'pm_updated_user_id')->textInput() ?>
<!---->
<!--    --><?php //= $form->field($model, 'pm_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
