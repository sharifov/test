<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\search\FlightQuoteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-quote-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'fq_id') ?>

    <?= $form->field($model, 'fq_flight_id') ?>

    <?= $form->field($model, 'fq_source_id') ?>

    <?= $form->field($model, 'fq_product_quote_id') ?>

    <?= $form->field($model, 'fq_hash_key') ?>

    <?php // echo $form->field($model, 'fq_service_fee_percent') ?>

    <?php // echo $form->field($model, 'fq_record_locator') ?>

    <?php // echo $form->field($model, 'fq_gds') ?>

    <?php // echo $form->field($model, 'fq_gds_pcc') ?>

    <?php // echo $form->field($model, 'fq_gds_offer_id') ?>

    <?php // echo $form->field($model, 'fq_type_id') ?>

    <?php // echo $form->field($model, 'fq_cabin_class') ?>

    <?php // echo $form->field($model, 'fq_trip_type_id') ?>

    <?php // echo $form->field($model, 'fq_main_airline') ?>

    <?php // echo $form->field($model, 'fq_fare_type_id') ?>

    <?php // echo $form->field($model, 'fq_created_user_id') ?>

    <?php // echo $form->field($model, 'fq_created_expert_id') ?>

    <?php // echo $form->field($model, 'fq_created_expert_name') ?>

    <?php // echo $form->field($model, 'fq_reservation_dump') ?>

    <?php // echo $form->field($model, 'fq_pricing_info') ?>

    <?php // echo $form->field($model, 'fq_origin_search_data') ?>

    <?php // echo $form->field($model, 'fq_last_ticket_date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
