<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuote */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-quote-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fq_flight_id')->textInput() ?>

    <?= $form->field($model, 'fq_source_id')->textInput() ?>

    <?= $form->field($model, 'fq_product_quote_id')->textInput() ?>

    <?= $form->field($model, 'fq_hash_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fq_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fq_service_fee_percent')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fq_record_locator')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fq_gds')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fq_gds_pcc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fq_gds_offer_id')->textInput() ?>

    <?= $form->field($model, 'fq_type_id')->textInput() ?>

    <?= $form->field($model, 'fq_cabin_class')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fq_trip_type_id')->textInput() ?>

    <?= $form->field($model, 'fq_main_airline')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fq_fare_type_id')->textInput() ?>

    <?= $form->field($model, 'fq_created_user_id')->textInput() ?>

    <?= $form->field($model, 'fq_created_expert_id')->textInput() ?>

    <?= $form->field($model, 'fq_created_expert_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fq_reservation_dump')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'fq_pricing_info')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'fq_origin_search_data')->textInput() ?>

    <?= $form->field($model, 'fq_last_ticket_date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
