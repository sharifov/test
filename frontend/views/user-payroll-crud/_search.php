<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\user\payroll\search\UserPayrollSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-payroll-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ups_id') ?>

    <?= $form->field($model, 'ups_user_id') ?>

    <?= $form->field($model, 'ups_month') ?>

    <?= $form->field($model, 'ups_year') ?>

    <?= $form->field($model, 'ups_base_amount') ?>

    <?php // echo $form->field($model, 'ups_profit_amount') ?>

    <?php // echo $form->field($model, 'ups_tax_amount') ?>

    <?php // echo $form->field($model, 'ups_payment_amount') ?>

    <?php // echo $form->field($model, 'ups_total_amount') ?>

    <?php // echo $form->field($model, 'ups_agent_status_id') ?>

    <?php // echo $form->field($model, 'ups_status_id') ?>

    <?php // echo $form->field($model, 'ups_created_dt') ?>

    <?php // echo $form->field($model, 'ups_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
