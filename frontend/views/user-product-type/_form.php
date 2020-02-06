<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserProductType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-product-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'upt_user_id')->textInput() ?>

    <?= $form->field($model, 'upt_product_type_id')->textInput() ?>

    <?= $form->field($model, 'upt_commission_percent')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'upt_product_enabled')->textInput() ?>

    <?= $form->field($model, 'upt_created_user_id')->textInput() ?>

    <?= $form->field($model, 'upt_updated_user_id')->textInput() ?>

    <?= $form->field($model, 'upt_created_dt')->textInput() ?>

    <?= $form->field($model, 'upt_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
