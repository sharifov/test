<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderTipsUserProfit\search\OrderTipsUserProfitSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-tips-user-profit-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'otup_order_id') ?>

    <?= $form->field($model, 'otup_user_id') ?>

    <?= $form->field($model, 'otup_percent') ?>

    <?= $form->field($model, 'otup_amount') ?>

    <?= $form->field($model, 'otup_created_dt') ?>

    <?php // echo $form->field($model, 'otup_updated_dt') ?>

    <?php // echo $form->field($model, 'otup_created_user_id') ?>

    <?php // echo $form->field($model, 'otup_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
