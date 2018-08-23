<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\QuotePriceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-price-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'quote_id') ?>

    <?= $form->field($model, 'passenger_type') ?>

    <?= $form->field($model, 'selling') ?>

    <?= $form->field($model, 'net') ?>

    <?php // echo $form->field($model, 'fare') ?>

    <?php // echo $form->field($model, 'taxes') ?>

    <?php // echo $form->field($model, 'mark_up') ?>

    <?php // echo $form->field($model, 'extra_mark_up') ?>

    <?php // echo $form->field($model, 'created') ?>

    <?php // echo $form->field($model, 'updated') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
