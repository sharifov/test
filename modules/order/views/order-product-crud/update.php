<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\order\src\entities\orderProduct\OrderProduct */

$this->title = 'Update Order Product: ' . $model->orp_order_id;
$this->params['breadcrumbs'][] = ['label' => 'Order Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->orp_order_id, 'url' => ['view', 'orp_order_id' => $model->orp_order_id, 'orp_product_quote_id' => $model->orp_product_quote_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-product-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
