<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\search\FlightPaxSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-pax-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'fp_id') ?>

    <?= $form->field($model, 'fp_flight_id') ?>

    <?= $form->field($model, 'fp_pax_id') ?>

    <?= $form->field($model, 'fp_pax_type') ?>

    <?= $form->field($model, 'fp_first_name') ?>

    <?php // echo $form->field($model, 'fp_last_name') ?>

    <?php // echo $form->field($model, 'fp_middle_name') ?>

    <?php // echo $form->field($model, 'fp_dob') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
