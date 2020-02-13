<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteStatusLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-quote-status-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'qsl_created_user_id')->textInput() ?>

    <?= $form->field($model, 'qsl_flight_quote_id')->textInput() ?>

    <?= $form->field($model, 'qsl_status_id')->textInput() ?>

    <?= $form->field($model, 'qsl_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
