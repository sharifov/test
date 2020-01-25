<?php

use modules\product\src\entities\productOption\ProductOptionQuery;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \modules\product\src\entities\productQuoteOption\ProductQuoteOption */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-quote-option-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
    <?= $form->field($model, 'pqo_product_quote_id')->input('number', ['min' => 0]) ?>

    <?= $form->field($model, 'pqo_product_option_id')->dropDownList(ProductOptionQuery::getList(), ['prompt' => '---']) ?>

    <?= $form->field($model, 'pqo_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pqo_description')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'pqo_status_id')->dropDownList(\modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus::getList(), ['prompt' => '---']) ?>

    <?= $form->field($model, 'pqo_price')->input('number', ['min' => 0, 'max' => 1000, 'step' => 0.01]) ?>

    <?= $form->field($model, 'pqo_client_price')->input('number', ['min' => 0, 'max' => 1000, 'step' => 0.01]) ?>

    <?= $form->field($model, 'pqo_extra_markup')->input('number', ['min' => 0, 'max' => 1000, 'step' => 0.01]) ?>

<!--    --><?//= $form->field($model, 'pqo_created_user_id')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'pqo_updated_user_id')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'pqo_created_dt')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'pqo_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
