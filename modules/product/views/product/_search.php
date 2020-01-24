<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\ProductSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pr_id') ?>

    <?= $form->field($model, 'pr_type_id') ?>

    <?= $form->field($model, 'pr_name') ?>

    <?= $form->field($model, 'pr_lead_id') ?>

    <?= $form->field($model, 'pr_description') ?>

    <?php // echo $form->field($model, 'pr_status_id') ?>

    <?php // echo $form->field($model, 'pr_service_fee_percent') ?>

    <?php // echo $form->field($model, 'pr_created_user_id') ?>

    <?php // echo $form->field($model, 'pr_updated_user_id') ?>

    <?php // echo $form->field($model, 'pr_created_dt') ?>

    <?php // echo $form->field($model, 'pr_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
