<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\kpi\entity\kpiUserProductCommission\search\KpiUserProductCommissionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="kpi-user-product-commission-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'upc_product_type_id') ?>

    <?= $form->field($model, 'upc_user_id') ?>

    <?= $form->field($model, 'upc_year') ?>

    <?= $form->field($model, 'upc_month') ?>

    <?= $form->field($model, 'upc_performance') ?>

    <?php // echo $form->field($model, 'upc_commission_percent') ?>

    <?php // echo $form->field($model, 'upc_created_user_id') ?>

    <?php // echo $form->field($model, 'upc_updated_user_id') ?>

    <?php // echo $form->field($model, 'upc_created_dt') ?>

    <?php // echo $form->field($model, 'upc_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
