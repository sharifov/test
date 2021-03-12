<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\src\entities\flightQuoteOption\search\FlightQuoteOptionSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="flight-quote-option-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'fqo_id') ?>

    <?= $form->field($model, 'fqo_product_quote_option_id') ?>

    <?= $form->field($model, 'fqo_flight_pax_id') ?>

    <?= $form->field($model, 'fqo_flight_quote_segment_id') ?>

    <?= $form->field($model, 'fqo_flight_quote_trip_id') ?>

    <?php // echo $form->field($model, 'fqo_display_name') ?>

    <?php // echo $form->field($model, 'fqo_markup_amount') ?>

    <?php // echo $form->field($model, 'fqo_base_price') ?>

    <?php // echo $form->field($model, 'fqo_total_price') ?>

    <?php // echo $form->field($model, 'fqo_client_total') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
