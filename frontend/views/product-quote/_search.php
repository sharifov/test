<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\ProductQuoteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-quote-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pq_id') ?>

    <?= $form->field($model, 'pq_gid') ?>

    <?= $form->field($model, 'pq_name') ?>

    <?= $form->field($model, 'pq_product_id') ?>

    <?= $form->field($model, 'pq_order_id') ?>

    <?php // echo $form->field($model, 'pq_description') ?>

    <?php // echo $form->field($model, 'pq_status_id') ?>

    <?php // echo $form->field($model, 'pq_price') ?>

    <?php // echo $form->field($model, 'pq_origin_price') ?>

    <?php // echo $form->field($model, 'pq_client_price') ?>

    <?php // echo $form->field($model, 'pq_service_fee_sum') ?>

    <?php // echo $form->field($model, 'pq_origin_currency') ?>

    <?php // echo $form->field($model, 'pq_client_currency') ?>

    <?php // echo $form->field($model, 'pq_origin_currency_rate') ?>

    <?php // echo $form->field($model, 'pq_client_currency_rate') ?>

    <?php // echo $form->field($model, 'pq_owner_user_id') ?>

    <?php // echo $form->field($model, 'pq_created_user_id') ?>

    <?php // echo $form->field($model, 'pq_updated_user_id') ?>

    <?php // echo $form->field($model, 'pq_created_dt') ?>

    <?php // echo $form->field($model, 'pq_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
