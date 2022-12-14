<?php

use common\models\Currency;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefund;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefundStatus;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\product\src\entities\productQuoteObjectRefund\search\ProductQuoteObjectRefundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Quote Object Refunds';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-object-refund-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Quote Object Refund', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
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
            [
                'attribute' => 'pqor_client_currency',
                'filter' => Currency::getList()
            ],
            'pqor_client_currency_rate',
            'pqor_client_selling_price',
            'pqor_client_refund_amount',
            'pqor_client_penalty_amount',
            'pqor_client_processing_fee_amount',
            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'pqor_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => 'Select User',
            ],
            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'pqor_updated_user_id',
                'relation' => 'updatedUser',
                'placeholder' => 'Select User',
            ],
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'pqor_created_dt',
                'options' => [
                    'width' => '200px;'
                ]
            ],
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'pqor_updated_dt',
                'options' => [
                    'width' => '200px;'
                ]
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
