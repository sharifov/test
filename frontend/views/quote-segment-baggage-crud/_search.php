<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\QuoteSegmentBaggageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-segment-baggage-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'qsb_id') ?>

    <?= $form->field($model, 'qsb_pax_code') ?>

    <?= $form->field($model, 'qsb_segment_id') ?>

    <?= $form->field($model, 'qsb_airline_code') ?>

    <?= $form->field($model, 'qsb_allow_pieces') ?>

    <?php // echo $form->field($model, 'qsb_allow_weight') ?>

    <?php // echo $form->field($model, 'qsb_allow_unit') ?>

    <?php // echo $form->field($model, 'qsb_allow_max_weight') ?>

    <?php // echo $form->field($model, 'qsb_allow_max_size') ?>

    <?php // echo $form->field($model, 'qsb_created_dt') ?>

    <?php // echo $form->field($model, 'qsb_updated_dt') ?>

    <?php // echo $form->field($model, 'qsb_updated_user_id') ?>

    <?php // echo $form->field($model, 'qsb_carry_one') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
