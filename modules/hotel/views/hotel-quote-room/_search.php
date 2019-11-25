<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\search\HotelQuoteRoomSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hotel-quote-room-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'hqr_id') ?>

    <?= $form->field($model, 'hqr_hotel_quote_id') ?>

    <?= $form->field($model, 'hqr_room_name') ?>

    <?= $form->field($model, 'hqr_key') ?>

    <?= $form->field($model, 'hqr_code') ?>

    <?php // echo $form->field($model, 'hqr_class') ?>

    <?php // echo $form->field($model, 'hqr_amount') ?>

    <?php // echo $form->field($model, 'hqr_currency') ?>

    <?php // echo $form->field($model, 'hqr_cancel_amount') ?>

    <?php // echo $form->field($model, 'hqr_cancel_from_dt') ?>

    <?php // echo $form->field($model, 'hqr_payment_type') ?>

    <?php // echo $form->field($model, 'hqr_board_code') ?>

    <?php // echo $form->field($model, 'hqr_board_name') ?>

    <?php // echo $form->field($model, 'hqr_rooms') ?>

    <?php // echo $form->field($model, 'hqr_adults') ?>

    <?php // echo $form->field($model, 'hqr_children') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
