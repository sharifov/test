<?php

use modules\order\src\entities\orderRefund\OrderRefund;
use modules\order\src\entities\orderRefund\OrderRefundClientStatus;
use modules\order\src\entities\orderRefund\OrderRefundStatus;
use modules\order\src\grid\columns\OrderColumn;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderRefund\OrderRefund */

$this->title = $model->orr_id;
$this->params['breadcrumbs'][] = ['label' => 'Order Refunds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-refund-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->orr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->orr_id], [
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
            'orr_id',
            'orr_uid',
            [
                'class' => OrderColumn::class,
                'attribute' => 'orr_order_id',
                'relation' => 'order'
            ],
            'orr_selling_price',
            'orr_penalty_amount',
            'orr_processing_fee_amount',
            'orr_charge_amount',
            'orr_refund_amount',
            [
                'attribute' => 'orr_client_status_id',
                'value' => static function (OrderRefund $model) {
                    return OrderRefundClientStatus::asFormat($model->orr_client_status_id);
                },
                'format' => 'raw',
                'filter' => OrderRefundClientStatus::getList()
            ],
            [
                'attribute' => 'orr_status_id',
                'value' => static function (OrderRefund $model) {
                    return OrderRefundStatus::asFormat($model->orr_status_id);
                },
                'format' => 'raw',
                'filter' => OrderRefundStatus::getList()
            ],
            'orr_client_currency',
            'orr_client_currency_rate',
            'orr_client_selling_price',
            'orr_client_charge_amount',
            'orr_client_refund_amount',
            'orr_description:ntext',
            'orr_expiration_dt:byUserDateTime',
            'orr_created_user_id:username',
            'orr_updated_user_id:username',
            'orr_created_dt:byUserDateTime',
            'orr_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
