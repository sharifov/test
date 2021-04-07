<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productHolder\ProductHolder */
/* @var $form ActiveForm */
?>

<div class="product-holder-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ph_product_id')->input('number') ?>

        <?= $form->field($model, 'ph_first_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ph_last_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ph_middle_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ph_email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ph_phone_number')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
