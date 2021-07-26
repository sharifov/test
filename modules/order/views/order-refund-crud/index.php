<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use common\models\Currency;
use modules\order\src\entities\orderRefund\OrderRefund;
use modules\order\src\entities\orderRefund\OrderRefundClientStatus;
use modules\order\src\entities\orderRefund\OrderRefundStatus;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\order\src\entities\orderRefund\search\OrderRefundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Refunds';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-refund-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Order Refund', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'orr_id',
            'orr_uid',
            'orr_order_id',
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
            [
                'attribute' => 'orr_client_currency',
                'filter' => Currency::getList()
            ],
            'orr_client_currency_rate',
            'orr_client_selling_price',
            'orr_client_charge_amount',
            'orr_client_refund_amount',
//            'orr_description:ntext',
            'orr_expiration_dt',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'orr_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => 'Select User',
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'orr_updated_user_id',
                'relation' => 'updatedUser',
                'placeholder' => 'Select User',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'orr_created_dt',
                'options' => [
                    'width' => '200px;'
                ]
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'orr_updated_dt',
                'options' => [
                    'width' => '200px;'
                ]
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
