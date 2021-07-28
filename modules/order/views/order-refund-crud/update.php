<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderRefund\OrderRefund */

$this->title = 'Update Order Refund: ' . $model->orr_id;
$this->params['breadcrumbs'][] = ['label' => 'Order Refunds', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->orr_id, 'url' => ['view', 'id' => $model->orr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-refund-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
