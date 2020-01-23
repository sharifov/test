<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteTrip */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-quote-trip-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fqt_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fqt_flight_quote_id')->textInput() ?>

    <?= $form->field($model, 'fqt_duration')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
