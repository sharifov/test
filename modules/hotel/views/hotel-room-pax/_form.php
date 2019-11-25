<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelRoomPax */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hotel-room-pax-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'hrp_hotel_room_id')->textInput() ?>

    <?= $form->field($model, 'hrp_type_id')->textInput() ?>

    <?= $form->field($model, 'hrp_age')->textInput() ?>

    <?= $form->field($model, 'hrp_first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hrp_last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hrp_dob')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
