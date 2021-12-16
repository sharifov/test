<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteData\search\ProductQuoteDataSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-quote-data-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pqd_id') ?>

    <?= $form->field($model, 'pqd_product_quote_id') ?>

    <?= $form->field($model, 'pqd_key') ?>

    <?= $form->field($model, 'pqd_value') ?>

    <?= $form->field($model, 'pqd_created_dt') ?>

    <?php // echo $form->field($model, 'pqd_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
