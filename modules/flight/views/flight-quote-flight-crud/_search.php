<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\search\FlightQuoteFlightSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="flight-quote-flight-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'fqf_id') ?>

    <?= $form->field($model, 'fqf_fq_id') ?>

    <?php // echo $form->field($model, 'fqf_type_id') ?>

    <?php // echo $form->field($model, 'fqf_cabin_class') ?>

    <?php // echo $form->field($model, 'fqf_trip_type_id') ?>

    <?php // echo $form->field($model, 'fqf_main_airline') ?>

    <?php // echo $form->field($model, 'fqf_fare_type_id') ?>

    <?php // echo $form->field($model, 'fqf_status_id') ?>

    <?php // echo $form->field($model, 'fqf_booking_id') ?>

    <?php // echo $form->field($model, 'fqf_pnr') ?>

    <?php // echo $form->field($model, 'fqf_validating_carrier') ?>

    <?php // echo $form->field($model, 'fqf_original_data_json') ?>

    <?php // echo $form->field($model, 'fqf_created_dt') ?>

    <?php // echo $form->field($model, 'fqf_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
