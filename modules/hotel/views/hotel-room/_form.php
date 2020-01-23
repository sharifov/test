<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelRoom */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hotel-room-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'hr_hotel_id')->textInput() ?>

    <?= $form->field($model, 'hr_room_name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
