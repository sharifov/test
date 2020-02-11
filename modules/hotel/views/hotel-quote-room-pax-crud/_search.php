<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\search\HotelQuoteRoomPaxSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hotel-quote-room-pax-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'hqrp_id') ?>

    <?= $form->field($model, 'hqrp_hotel_room_id') ?>

    <?= $form->field($model, 'hqrp_type_id') ?>

    <?= $form->field($model, 'hqrp_age') ?>

    <?= $form->field($model, 'hqrp_first_name') ?>

    <?php // echo $form->field($model, 'hqrp_last_name') ?>

    <?php // echo $form->field($model, 'hqrp_dob') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
