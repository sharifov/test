<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadFlightSegmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-flight-segment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'lead_id') ?>

    <?= $form->field($model, 'origin') ?>

    <?= $form->field($model, 'destination') ?>

    <?= $form->field($model, 'departure') ?>

    <?php // echo $form->field($model, 'created') ?>

    <?php // echo $form->field($model, 'updated') ?>

    <?php // echo $form->field($model, 'flexibility') ?>

    <?php // echo $form->field($model, 'flexibility_type') ?>

    <?php // echo $form->field($model, 'origin_label') ?>

    <?php // echo $form->field($model, 'destination_label') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
