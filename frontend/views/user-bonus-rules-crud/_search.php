<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\UserBonusRulesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-bonus-rules-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ubr_exp_month') ?>

    <?= $form->field($model, 'ubr_kpi_percent') ?>

    <?= $form->field($model, 'ubr_order_profit') ?>

    <?= $form->field($model, 'ubr_value') ?>

    <?= $form->field($model, 'ubr_created_user_id') ?>

    <?php // echo $form->field($model, 'ubr_updated_user_id') ?>

    <?php // echo $form->field($model, 'ubr_created_dt') ?>

    <?php // echo $form->field($model, 'ubr_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
