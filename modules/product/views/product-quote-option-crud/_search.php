<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \modules\product\src\entities\productQuoteOption\search\ProductQuoteOptionCrudSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-quote-option-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pqo_id') ?>

    <?= $form->field($model, 'pqo_product_quote_id') ?>

    <?= $form->field($model, 'pqo_product_option_id') ?>

    <?= $form->field($model, 'pqo_name') ?>

    <?= $form->field($model, 'pqo_description') ?>

    <?php // echo $form->field($model, 'pqo_status_id') ?>

    <?php // echo $form->field($model, 'pqo_price') ?>

    <?php // echo $form->field($model, 'pqo_client_price') ?>

    <?php // echo $form->field($model, 'pqo_extra_markup') ?>

    <?php // echo $form->field($model, 'pqo_created_user_id') ?>

    <?php // echo $form->field($model, 'pqo_updated_user_id') ?>

    <?php // echo $form->field($model, 'pqo_created_dt') ?>

    <?php // echo $form->field($model, 'pqo_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
