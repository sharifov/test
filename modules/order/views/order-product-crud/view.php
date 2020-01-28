<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \modules\order\src\entities\orderProduct\OrderProduct */

$this->title = $model->orp_order_id;
$this->params['breadcrumbs'][] = ['label' => 'Order Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'orp_order_id' => $model->orp_order_id, 'orp_product_quote_id' => $model->orp_product_quote_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'orp_order_id' => $model->orp_order_id, 'orp_product_quote_id' => $model->orp_product_quote_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'orpOrder:order',
            'orpProductQuote:productQuote',
            'orpCreatedUser:userName',
            'orp_created_dt:byUserDateTime',
        ],
    ]) ?>

</div>
