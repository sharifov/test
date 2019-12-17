<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProductQuoteOption */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-quote-option-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pqo_product_quote_id')->textInput() ?>

    <?= $form->field($model, 'pqo_product_option_id')->textInput() ?>

    <?= $form->field($model, 'pqo_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pqo_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pqo_status_id')->textInput() ?>

    <?= $form->field($model, 'pqo_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pqo_client_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pqo_extra_markup')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pqo_created_user_id')->textInput() ?>

    <?= $form->field($model, 'pqo_updated_user_id')->textInput() ?>

    <?= $form->field($model, 'pqo_created_dt')->textInput() ?>

    <?= $form->field($model, 'pqo_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
