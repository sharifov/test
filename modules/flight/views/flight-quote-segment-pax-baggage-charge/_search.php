<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\search\FlightQuoteSegmentPaxBaggageChargeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-quote-segment-pax-baggage-charge-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'qsbc_id') ?>

    <?= $form->field($model, 'qsbc_flight_pax_code_id') ?>

    <?= $form->field($model, 'qsbc_flight_quote_segment_id') ?>

    <?= $form->field($model, 'qsbc_first_piece') ?>

    <?= $form->field($model, 'qsbc_last_piece') ?>

    <?php // echo $form->field($model, 'qsbc_origin_price') ?>

    <?php // echo $form->field($model, 'qsbc_origin_currency') ?>

    <?php // echo $form->field($model, 'qsbc_price') ?>

    <?php // echo $form->field($model, 'qsbc_client_price') ?>

    <?php // echo $form->field($model, 'qsbc_client_currency') ?>

    <?php // echo $form->field($model, 'qsbc_max_weight') ?>

    <?php // echo $form->field($model, 'qsbc_max_size') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
