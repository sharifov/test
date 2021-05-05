<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\search\FlightQuoteBookingAirlineSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="flight-quote-booking-airline-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'fqba_id') ?>

    <?= $form->field($model, 'fqba_fqb_id') ?>

    <?= $form->field($model, 'fqba_record_locator') ?>

    <?= $form->field($model, 'fqba_airline_code') ?>

    <?= $form->field($model, 'fqba_created_dt') ?>

    <?php // echo $form->field($model, 'fqba_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
