<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteRefund\ProductQuoteChangeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-quote-change-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'pqc_id') ?>

    <?= $form->field($model, 'pqc_pq_id') ?>

    <?= $form->field($model, 'pqc_case_id') ?>

    <?= $form->field($model, 'pqc_decision_user') ?>

    <?= $form->field($model, 'pqc_status_id') ?>

    <?php // echo $form->field($model, 'pqc_decision_type_id') ?>

    <?php // echo $form->field($model, 'pqc_created_dt') ?>

    <?php // echo $form->field($model, 'pqc_updated_dt') ?>

    <?php // echo $form->field($model, 'pqc_decision_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
