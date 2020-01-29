<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \modules\product\src\entities\productOption\search\ProductOptionCrudSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-option-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'po_id') ?>

    <?= $form->field($model, 'po_key') ?>

    <?= $form->field($model, 'po_product_type_id') ?>

    <?= $form->field($model, 'po_name') ?>

    <?= $form->field($model, 'po_description') ?>

    <?php // echo $form->field($model, 'po_price_type_id') ?>

    <?php // echo $form->field($model, 'po_max_price') ?>

    <?php // echo $form->field($model, 'po_min_price') ?>

    <?php // echo $form->field($model, 'po_price') ?>

    <?php // echo $form->field($model, 'po_enabled') ?>

    <?php // echo $form->field($model, 'po_created_user_id') ?>

    <?php // echo $form->field($model, 'po_updated_user_id') ?>

    <?php // echo $form->field($model, 'po_created_dt') ?>

    <?php // echo $form->field($model, 'po_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
