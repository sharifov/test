<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteFlight */
/* @var $form ActiveForm */
?>

<div class="flight-quote-flight-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'fqf_fq_id')->textInput() ?>

        <?= $form->field($model, 'fqf_record_locator')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqf_gds')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqf_gds_pcc')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqf_type_id')->textInput() ?>

        <?= $form->field($model, 'fqf_cabin_class')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqf_trip_type_id')->textInput() ?>

        <?= $form->field($model, 'fqf_main_airline')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqf_fare_type_id')->textInput() ?>

        <?= $form->field($model, 'fqf_status_id')->textInput() ?>

        <?= $form->field($model, 'fqf_booking_id')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqf_pnr')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqf_validating_carrier')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqf_original_data_json')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>