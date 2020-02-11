<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelQuoteRoomPax */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hotel-quote-room-pax-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'hqrp_hotel_room_id')->textInput() ?>

    <?= $form->field($model, 'hqrp_type_id')->textInput() ?>

    <?= $form->field($model, 'hqrp_age')->textInput() ?>

    <?= $form->field($model, 'hqrp_first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hqrp_last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hqrp_dob')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
