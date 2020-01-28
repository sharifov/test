<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\invoice\src\entities\invoiceStatusLog\search\InvoiceStatusLogCrudSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="invoice-status-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'invsl_id') ?>

    <?= $form->field($model, 'invsl_product_quote_id') ?>

    <?= $form->field($model, 'invsl_start_status_id') ?>

    <?= $form->field($model, 'invsl_end_status_id') ?>

    <?= $form->field($model, 'invsl_start_dt') ?>

    <?php // echo $form->field($model, 'invsl_end_dt') ?>

    <?php // echo $form->field($model, 'invsl_duration') ?>

    <?php // echo $form->field($model, 'invsl_description') ?>

    <?php // echo $form->field($model, 'invsl_created_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
