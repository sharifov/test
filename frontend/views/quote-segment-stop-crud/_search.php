<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\QuoteSegmentStopSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-segment-stop-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'qss_id') ?>

    <?= $form->field($model, 'qss_location_code') ?>

    <?= $form->field($model, 'qss_departure_dt') ?>

    <?= $form->field($model, 'qss_arrival_dt') ?>

    <?= $form->field($model, 'qss_duration') ?>

    <?php // echo $form->field($model, 'qss_elapsed_time') ?>

    <?php // echo $form->field($model, 'qss_equipment') ?>

    <?php // echo $form->field($model, 'qss_segment_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
