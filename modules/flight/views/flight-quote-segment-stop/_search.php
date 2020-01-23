<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\search\FlightQuoteSegmentStopSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-quote-segment-stop-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'qss_id') ?>

    <?= $form->field($model, 'qss_quote_segment_id') ?>

    <?= $form->field($model, 'qss_location_iata') ?>

    <?= $form->field($model, 'qss_equipment') ?>

    <?= $form->field($model, 'qss_elapsed_time') ?>

    <?php // echo $form->field($model, 'qss_duration') ?>

    <?php // echo $form->field($model, 'qss_departure_dt') ?>

    <?php // echo $form->field($model, 'qss_arrival_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
