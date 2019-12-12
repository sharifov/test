<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OfferProduct */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="offer-product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'op_offer_id')->textInput() ?>

    <?= $form->field($model, 'op_product_quote_id')->textInput() ?>

    <?= $form->field($model, 'op_created_user_id')->textInput() ?>

    <?//= $form->field($model, 'op_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
