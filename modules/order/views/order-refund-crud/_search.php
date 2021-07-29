<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderRefund\search\OrderRefundSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-refund-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'orr_id') ?>

    <?= $form->field($model, 'orr_uid') ?>

    <?= $form->field($model, 'orr_order_id') ?>

    <?= $form->field($model, 'orr_selling_price') ?>

    <?= $form->field($model, 'orr_penalty_amount') ?>

    <?php // echo $form->field($model, 'orr_processing_fee_amount') ?>

    <?php // echo $form->field($model, 'orr_charge_amount') ?>

    <?php // echo $form->field($model, 'orr_refund_amount') ?>

    <?php // echo $form->field($model, 'orr_client_status_id') ?>

    <?php // echo $form->field($model, 'orr_status_id') ?>

    <?php // echo $form->field($model, 'orr_client_currency') ?>

    <?php // echo $form->field($model, 'orr_client_currency_rate') ?>

    <?php // echo $form->field($model, 'orr_client_selling_price') ?>

    <?php // echo $form->field($model, 'orr_client_charge_amount') ?>

    <?php // echo $form->field($model, 'orr_client_refund_amount') ?>

    <?php // echo $form->field($model, 'orr_description') ?>

    <?php // echo $form->field($model, 'orr_expiration_dt') ?>

    <?php // echo $form->field($model, 'orr_created_user_id') ?>

    <?php // echo $form->field($model, 'orr_updated_user_id') ?>

    <?php // echo $form->field($model, 'orr_created_dt') ?>

    <?php // echo $form->field($model, 'orr_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
