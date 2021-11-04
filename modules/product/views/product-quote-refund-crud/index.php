<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use common\models\Currency;
use frontend\helpers\JsonHelper;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\product\src\entities\productQuoteRefund\search\ProductQuoteRefundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Quote Refunds';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-refund-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Quote Refund', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'pqr_id',
            'pqr_gid',
            'pqr_order_refund_id',
            'pqr_case_id',
            'pqr_cid',
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
            [
                'attribute' => 'pqr_client_currency',
                'filter' => Currency::getList()
            ],
            [
                'attribute' => 'pqr_type_id',
                'value' => static function (ProductQuoteRefund $model) {
                    return $model->getTypeName();
                },
                'format' => 'raw',
                'filter' => ProductQuoteRefund::getTypeList()
            ],
            'pqr_client_currency_rate',
            'pqr_client_selling_price',
            'pqr_client_penalty_amount',
            'pqr_client_processing_fee_amount',
            'pqr_client_refund_amount',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'pqr_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => 'Select User',
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'pqr_updated_user_id',
                'relation' => 'updatedUser',
                'placeholder' => 'Select User',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pqr_created_dt',
                'options' => [
                    'width' => '200px;'
                ]
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pqr_updated_dt',
                'options' => [
                    'width' => '200px;'
                ]
            ],
            [
                'attribute' => 'pqr_data_json',
                'value' => static function (ProductQuoteRefund $model) {
                    $content = '<p>' . StringHelper::truncate(JsonHelper::encode($model->pqr_data_json), 216, '...', null, true) . '</p>';
                    $content .= Html::a(
                        '<i class="fas fa-eye"></i> details</a>',
                        null,
                        [
                            'class' => 'btn btn-sm btn-success',
                            'data-pjax' => 0,
                            'onclick' => '(function ( $event ) { $("#data_' . $model->pqr_id . '").toggle(); })();',
                        ]
                    );
                    $content .= $model->pqr_data_json ?
                        '<pre id="data_' . $model->pqr_id . '" style="display: none;">' .
                        VarDumper::dumpAsString(JsonHelper::decode($model->pqr_data_json), 10, true) . '</pre>' : '-';

                    return $content;
                },
                'format' => 'raw',
                'contentOptions' => [
                    'style' => ['max-width' => '800px', 'word-wrap' => 'break-word !important'],
                ],
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
