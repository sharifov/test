<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\InvoiceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="invoice-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'inv_id') ?>

    <?= $form->field($model, 'inv_gid') ?>

    <?= $form->field($model, 'inv_uid') ?>

    <?= $form->field($model, 'inv_order_id') ?>

    <?= $form->field($model, 'inv_status_id') ?>

    <?php // echo $form->field($model, 'inv_sum') ?>

    <?php // echo $form->field($model, 'inv_client_sum') ?>

    <?php // echo $form->field($model, 'inv_client_currency') ?>

    <?php // echo $form->field($model, 'inv_currency_rate') ?>

    <?php // echo $form->field($model, 'inv_description') ?>

    <?php // echo $form->field($model, 'inv_created_user_id') ?>

    <?php // echo $form->field($model, 'inv_updated_user_id') ?>

    <?php // echo $form->field($model, 'inv_created_dt') ?>

    <?php // echo $form->field($model, 'inv_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
