<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\search\FlightSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'fl_id') ?>

    <?= $form->field($model, 'fl_product_id') ?>

    <?= $form->field($model, 'fl_trip_type_id') ?>

    <?= $form->field($model, 'fl_cabin_class') ?>

    <?= $form->field($model, 'fl_adults') ?>

    <?php // echo $form->field($model, 'fl_children') ?>

    <?php // echo $form->field($model, 'fl_infants') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
