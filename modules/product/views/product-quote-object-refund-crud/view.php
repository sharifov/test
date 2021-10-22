<?php

use frontend\helpers\JsonHelper;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefund;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefundStatus;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefund */

$this->title = $model->pqor_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Object Refunds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-quote-object-refund-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->pqor_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->pqor_id], [
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
            'pqor_id',
            'pqor_product_quote_refund_id',
            'pqor_quote_object_id',
            'pqor_title',
            'pqor_selling_price',
            'pqor_penalty_amount',
            'pqor_processing_fee_amount',
            'pqor_refund_amount',
            [
                'attribute' => 'pqor_status_id',
                'value' => static function (ProductQuoteObjectRefund $model) {
                    return ProductQuoteObjectRefundStatus::asFormat($model->pqor_status_id);
                },
                'format' => 'raw',
                'filter' => ProductQuoteObjectRefundStatus::getList()
            ],
            'pqor_client_currency',
            'pqor_client_currency_rate',
            'pqor_client_selling_price',
            'pqor_client_refund_amount',
            'pqor_created_user_id:username',
            'pqor_updated_user_id:username',
            'pqor_created_dt:byUserDateTime',
            'pqor_updated_dt:byUserDateTime',
            [
                'attribute' => 'pqor_data_json',
                'value' => static function (ProductQuoteObjectRefund $model) {
                    return '<pre>' . VarDumper::dumpAsString(JsonHelper::decode($model->pqor_data_json), 10, true) . '</pre>';
                },
                'format' => 'raw',
            ],
        ],
    ]) ?>

</div>
