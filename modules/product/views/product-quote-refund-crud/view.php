<?php

use frontend\helpers\JsonHelper;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteRefund\ProductQuoteRefund */

$this->title = $model->pqr_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Refunds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-quote-refund-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->pqr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->pqr_id], [
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
            'pqr_id',
            'pqr_gid',
            'pqr_order_refund_id',
            'pqr_product_quote_id',
            'pqr_selling_price',
            'pqr_penalty_amount',
            'pqr_processing_fee_amount',
            'pqr_refund_amount',
            [
                'attribute' => 'pqr_status_id',
                'value' => static function (ProductQuoteRefund $model) {
                    return ProductQuoteRefundStatus::asFormat($model->pqr_status_id);
                },
                'format' => 'raw',
                'filter' => ProductQuoteRefundStatus::getList()
            ],
            'pqr_client_currency',
            'pqr_client_currency_rate',
            'pqr_client_selling_price',
            'pqr_client_refund_amount',
            'pqr_created_user_id:username',
            'pqr_updated_user_id:username',
            'pqr_created_dt:byUserDateTime',
            'pqr_updated_dt:byUserDateTime',
            'pqr_case_id',
            'pqr_cid',
            [
                'attribute' => 'pqr_type_id',
                'value' => static function (ProductQuoteRefund $model) {
                    return $model->getTypeName();
                },
                'format' => 'raw',
                'filter' => ProductQuoteRefund::getTypeList()
            ],
            [
                'attribute' => 'pqr_data_json',
                'value' => static function (ProductQuoteRefund $model) {
                    return '<pre>' . VarDumper::dumpAsString(JsonHelper::decode($model->pqr_data_json), 10, true) . '</pre>';
                },
                'format' => 'raw',
            ],
        ],
    ]) ?>

</div>
