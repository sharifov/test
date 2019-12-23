<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\CurrencyHistorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="currency-history-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cur_his_code') ?>

    <?= $form->field($model, 'cur_his_base_rate') ?>

    <?= $form->field($model, 'cur_his_app_rate') ?>

    <?= $form->field($model, 'cur_his_app_percent') ?>

    <?= $form->field($model, 'cur_his_created') ?>

    <?php // echo $form->field($model, 'cur_his_main_created_dt') ?>

    <?php // echo $form->field($model, 'cur_his_main_updated_dt') ?>

    <?php // echo $form->field($model, 'cur_his_main_synch_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
