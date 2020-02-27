<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\kpi\entity\kpiProductCommission\search\KpiProductCommissionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="kpi-product-commission-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pc_product_type_id') ?>

    <?= $form->field($model, 'pc_performance') ?>

    <?= $form->field($model, 'pc_commission_percent') ?>

    <?= $form->field($model, 'pc_created_user_id') ?>

    <?= $form->field($model, 'pc_updated_user_id') ?>

    <?php // echo $form->field($model, 'pc_created_dt') ?>

    <?php // echo $form->field($model, 'pc_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
