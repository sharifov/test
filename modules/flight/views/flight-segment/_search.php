<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\search\FlightSegmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-segment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'fs_id') ?>

    <?= $form->field($model, 'fs_flight_id') ?>

    <?= $form->field($model, 'fs_origin_iata') ?>

    <?= $form->field($model, 'fs_destination_iata') ?>

    <?= $form->field($model, 'fs_departure_date') ?>

    <?php // echo $form->field($model, 'fs_flex_type_id') ?>

    <?php // echo $form->field($model, 'fs_flex_days') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
