<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\coupon\entity\couponCase\CouponCase */

$this->title = 'Update Coupon Case: ' . $model->cc_coupon_id;
$this->params['breadcrumbs'][] = ['label' => 'Coupon Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cc_coupon_id, 'url' => ['view', 'cc_coupon_id' => $model->cc_coupon_id, 'cc_case_id' => $model->cc_case_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="coupon-case-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
