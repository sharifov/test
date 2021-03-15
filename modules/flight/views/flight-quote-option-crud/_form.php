<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\src\entities\flightQuoteOption\FlightQuoteOption */
/* @var $form ActiveForm */
?>

<div class="flight-quote-option-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'fqo_product_quote_option_id')->textInput() ?>

        <?= $form->field($model, 'fqo_flight_pax_id')->textInput() ?>

        <?= $form->field($model, 'fqo_flight_quote_segment_id')->textInput() ?>

        <?= $form->field($model, 'fqo_flight_quote_trip_id')->textInput() ?>

        <?= $form->field($model, 'fqo_display_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqo_markup_amount')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqo_base_price')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqo_total_price')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqo_client_total')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
