<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderUserProfit\search\OrderUserProfitSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-user-profit-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'oup_order_id') ?>

    <?= $form->field($model, 'oup_user_id') ?>

    <?= $form->field($model, 'oup_percent') ?>

    <?= $form->field($model, 'oup_amount') ?>

    <?= $form->field($model, 'oup_created_dt') ?>

    <?php // echo $form->field($model, 'oup_updated_dt') ?>

    <?php // echo $form->field($model, 'oup_created_user_id') ?>

    <?php // echo $form->field($model, 'oup_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
