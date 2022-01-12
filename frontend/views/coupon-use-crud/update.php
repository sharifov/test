<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\coupon\entity\couponUse\CouponUse */

$this->title = 'Update Coupon Use: ' . $model->cu_id;
$this->params['breadcrumbs'][] = ['label' => 'Coupon Uses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cu_id, 'url' => ['view', 'id' => $model->cu_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="coupon-use-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
