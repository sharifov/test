<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \modules\product\src\entities\productQuote\ProductQuote */

$this->title = $model->pq_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-quote-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->pq_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->pq_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Status Log', ['/product/product-quote-status-log-crud/index', 'ProductQuoteStatusLogCrudSearch[pqsl_product_quote_id]' => $model->pq_id], ['class' => 'btn btn-warning']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'pq_id',
            'pq_gid',
            'clone:productQuote',
            'pq_name',
            'pqProduct:product',
            'pq_order_id',
            'pq_description:ntext',
            'pq_status_id:productQuoteStatus',
            'pq_price',
            'pq_origin_price',
            'pq_client_price',
            'pq_service_fee_sum',
            'pq_origin_currency',
            'pq_client_currency',
            'pq_origin_currency_rate',
            'pq_client_currency_rate',
            'pqOwnerUser:userName',
            'pqCreatedUser:userName',
            'pqUpdatedUser:userName',
            'pq_created_dt:byUserDateTime',
            'pq_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
