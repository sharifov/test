<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderStatusLog\search\OrderStatusLogCrudSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-status-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'orsl_id') ?>

    <?= $form->field($model, 'orsl_product_quote_id') ?>

    <?= $form->field($model, 'orsl_start_status_id') ?>

    <?= $form->field($model, 'orsl_end_status_id') ?>

    <?= $form->field($model, 'orsl_start_dt') ?>

    <?php // echo $form->field($model, 'orsl_end_dt') ?>

    <?php // echo $form->field($model, 'orsl_duration') ?>

    <?php // echo $form->field($model, 'orsl_description') ?>

    <?php // echo $form->field($model, 'orsl_owner_user_id') ?>

    <?php // echo $form->field($model, 'orsl_created_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
