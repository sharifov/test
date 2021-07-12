<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\coupon\entity\couponClient\CouponClient */

$this->title = 'Update Coupon Client: ' . $model->cuc_id;
$this->params['breadcrumbs'][] = ['label' => 'Coupon Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cuc_id, 'url' => ['view', 'id' => $model->cuc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="coupon-client-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
