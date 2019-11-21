<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\CurrencySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="currency-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cur_code') ?>

    <?= $form->field($model, 'cur_name') ?>

    <?= $form->field($model, 'cur_symbol') ?>

    <?= $form->field($model, 'cur_base_rate') ?>

    <?= $form->field($model, 'cur_app_rate') ?>

    <?php // echo $form->field($model, 'cur_app_percent') ?>

    <?php // echo $form->field($model, 'cur_enabled') ?>

    <?php // echo $form->field($model, 'cur_default') ?>

    <?php // echo $form->field($model, 'cur_sort_order') ?>

    <?php // echo $form->field($model, 'cur_created_dt') ?>

    <?php // echo $form->field($model, 'cur_updated_dt') ?>

    <?php // echo $form->field($model, 'cur_synch_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
