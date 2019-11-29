<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\forms\HotelRoomForm */
/* @var $form yii\bootstrap4\ActiveForm */

$pjaxId = 'pjax-hotel-room-form';
?>

<div class="hotel-room-form">

    <script>
        pjaxOffFormSubmit('#<?=$pjaxId?>');
    </script>
    <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
        <?php
        $form = ActiveForm::begin([
            'options' => ['data-pjax' => true],
            'action' => ['/hotel/hotel-room/create-ajax', 'id' => $model->hr_hotel_id],
            'method' => 'post'
        ]);
        ?>

        <?= $form->field($model, 'hr_hotel_id')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'hr_room_name')->textInput(['maxlength' => true]) ?>

        <div class="form-group text-center">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>
