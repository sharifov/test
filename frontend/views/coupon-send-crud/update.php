<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\coupon\entity\couponSend\CouponSend */

$this->title = 'Update Coupon Send: ' . $model->cus_id;
$this->params['breadcrumbs'][] = ['label' => 'Coupon Sends', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cus_id, 'url' => ['view', 'id' => $model->cus_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="coupon-send-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
