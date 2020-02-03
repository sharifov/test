<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\search\FlightQuoteSegmentPaxBaggageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-quote-segment-pax-baggage-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'qsb_id') ?>

    <?= $form->field($model, 'qsb_flight_pax_code_id') ?>

    <?= $form->field($model, 'qsb_flight_quote_segment_id') ?>

    <?= $form->field($model, 'qsb_airline_code') ?>

    <?= $form->field($model, 'qsb_allow_pieces') ?>

    <?php // echo $form->field($model, 'qsb_allow_weight') ?>

    <?php // echo $form->field($model, 'qsb_allow_unit') ?>

    <?php // echo $form->field($model, 'qsb_allow_max_weight') ?>

    <?php // echo $form->field($model, 'qsb_allow_max_size') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
