<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\search\HotelRoomPaxSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hotel-room-pax-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'hrp_id') ?>

    <?= $form->field($model, 'hrp_hotel_room_id') ?>

    <?= $form->field($model, 'hrp_type_id') ?>

    <?= $form->field($model, 'hrp_age') ?>

    <?= $form->field($model, 'hrp_first_name') ?>

    <?php // echo $form->field($model, 'hrp_last_name') ?>

    <?php // echo $form->field($model, 'hrp_dob') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
