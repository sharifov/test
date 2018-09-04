<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\QuoteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'uid') ?>

    <?= $form->field($model, 'lead_id') ?>

    <?= $form->field($model, 'employee_id') ?>

    <?= $form->field($model, 'record_locator') ?>

    <?php // echo $form->field($model, 'pcc') ?>

    <?php // echo $form->field($model, 'cabin') ?>

    <?php // echo $form->field($model, 'gds') ?>

    <?php // echo $form->field($model, 'trip_type') ?>

    <?php // echo $form->field($model, 'main_airline_code') ?>

    <?php // echo $form->field($model, 'reservation_dump') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'check_payment') ?>

    <?php // echo $form->field($model, 'fare_type') ?>

    <?php // echo $form->field($model, 'created') ?>

    <?php // echo $form->field($model, 'updated') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
