<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\coupon\entity\couponProduct\CouponProduct */

$this->title = 'Update Coupon Product: ' . $model->cup_coupon_id;
$this->params['breadcrumbs'][] = ['label' => 'Coupon Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cup_coupon_id, 'url' => ['view', 'cup_coupon_id' => $model->cup_coupon_id, 'cup_product_type_id' => $model->cup_product_type_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="coupon-product-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
