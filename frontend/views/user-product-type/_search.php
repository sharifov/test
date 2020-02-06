<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\UserProductTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-product-type-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'upt_user_id') ?>

    <?= $form->field($model, 'upt_product_type_id') ?>

    <?= $form->field($model, 'upt_commission_percent') ?>

    <?= $form->field($model, 'upt_product_enabled') ?>

    <?= $form->field($model, 'upt_created_user_id') ?>

    <?php // echo $form->field($model, 'upt_updated_user_id') ?>

    <?php // echo $form->field($model, 'upt_created_dt') ?>

    <?php // echo $form->field($model, 'upt_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
