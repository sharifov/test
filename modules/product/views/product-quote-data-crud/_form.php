<?php

use modules\product\src\entities\productQuoteData\ProductQuoteDataKey;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteData\ProductQuoteData */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-quote-data-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pqd_product_quote_id')->input('number') ?>

    <?= $form->field($model, 'pqd_key')->dropDownList(ProductQuoteDataKey::getList(), ['prompt' => '---']) ?>

    <?= $form->field($model, 'pqd_value')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
