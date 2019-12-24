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

    <?= $form->field($model, 'ch_code') ?>

    <?= $form->field($model, 'ch_base_rate') ?>

    <?= $form->field($model, 'ch_app_rate') ?>

    <?= $form->field($model, 'ch_app_percent') ?>

    <?= $form->field($model, 'ch_created_date') ?>

    <?php // echo $form->field($model, 'ch_main_created_dt') ?>

    <?php // echo $form->field($model, 'ch_main_updated_dt') ?>

    <?php // echo $form->field($model, 'ch_main_synch_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
