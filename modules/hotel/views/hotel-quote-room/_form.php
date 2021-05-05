<?php

use kdn\yii2\JsonEditor;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelQuoteRoom */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hotel-quote-room-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'hqr_hotel_quote_id')->textInput() ?>

    <?= $form->field($model, 'hqr_room_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hqr_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hqr_code')->textInput() ?>

    <?= $form->field($model, 'hqr_class')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hqr_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hqr_currency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hqr_payment_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hqr_board_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hqr_board_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hqr_rooms')->textInput() ?>

    <?= $form->field($model, 'hqr_adults')->textInput() ?>

    <?= $form->field($model, 'hqr_children')->textInput() ?>

    <?= $form->field($model, 'hqr_cancellation_policies')->widget(JsonEditor::class, [
        'model' => $model,
        'name' => 'hqr_cancellation_policies',
        'value' => \yii\helpers\Json::encode($model->hqr_cancellation_policies)
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
