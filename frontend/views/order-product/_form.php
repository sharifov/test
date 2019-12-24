<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OrderProduct */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'orp_order_id')->textInput() ?>

    <?= $form->field($model, 'orp_product_quote_id')->textInput() ?>

    <?= $form->field($model, 'orp_created_user_id')->textInput() ?>

    <?//= $form->field($model, 'orp_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
