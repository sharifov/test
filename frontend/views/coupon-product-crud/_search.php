<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\coupon\entity\couponProduct\CouponProductSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="coupon-product-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cup_coupon_id') ?>

    <?= $form->field($model, 'cup_product_type_id') ?>

    <?= $form->field($model, 'cup_data_json') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
