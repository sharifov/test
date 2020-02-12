<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\user\payment\search\UserPaymentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-payment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'upt_id') ?>

    <?= $form->field($model, 'upt_assigned_user_id') ?>

    <?= $form->field($model, 'upt_category_id') ?>

    <?= $form->field($model, 'upt_status_id') ?>

    <?= $form->field($model, 'upt_amount') ?>

    <?php // echo $form->field($model, 'upt_description') ?>

    <?php // echo $form->field($model, 'upt_date') ?>

    <?php // echo $form->field($model, 'upt_created_user_id') ?>

    <?php // echo $form->field($model, 'upt_updated_user_id') ?>

    <?php // echo $form->field($model, 'upt_created_dt') ?>

    <?php // echo $form->field($model, 'upt_updated_dt') ?>

    <?php // echo $form->field($model, 'upt_payroll_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
