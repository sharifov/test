<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\coupon\entity\couponUserAction\CouponUserAction */

$this->title = 'Update Coupon User Action: ' . $model->cuu_id;
$this->params['breadcrumbs'][] = ['label' => 'Coupon User Actions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cuu_id, 'url' => ['view', 'id' => $model->cuu_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="coupon-user-action-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
