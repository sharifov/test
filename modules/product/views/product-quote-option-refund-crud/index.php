<?php

use common\models\Currency;
use frontend\helpers\JsonHelper;
use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefund;
use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefundStatus;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\product\src\entities\productQuoteOptionRefund\search\ProductQuoteOptionRefundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Quote Option Refunds';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-option-refund-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Quote Option Refund', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'pqor_id',
            'pqor_product_quote_refund_id',
            'pqor_product_quote_option_id',
            'pqor_selling_price',
            'pqor_penalty_amount',
            'pqor_processing_fee_amount',
            'pqor_refund_amount',
            [
                'attribute' => 'pqor_status_id',
                'value' => static function (ProductQuoteOptionRefund $model) {
                    return ProductQuoteOptionRefundStatus::asFormat($model->pqor_status_id);
                },
                'format' => 'raw',
                'filter' => ProductQuoteOptionRefundStatus::getList()
            ],
            [
                'attribute' => 'pqor_client_currency',
                'filter' => Currency::getList()
            ],
            'pqor_client_currency_rate',
            'pqor_client_selling_price',
            'pqor_client_refund_amount',
            'pqor_refund_allow:booleanByLabel',
            [
                'attribute' => 'pqor_data_json',
                'value' => static function (ProductQuoteOptionRefund $model) {
                    $content = '<p>' . StringHelper::truncate(JsonHelper::encode($model->pqor_data_json), 216, '...', null, true) . '</p>';
                    $content .= Html::a(
                        '<i class="fas fa-eye"></i> details</a>',
                        null,
                        [
                            'class' => 'btn btn-sm btn-success',
                            'data-pjax' => 0,
                            'onclick' => '(function ( $event ) { $("#data_' . $model->pqor_id . '").toggle(); })();',
                        ]
                    );
                    $content .= $model->pqor_data_json ?
                        '<pre id="data_' . $model->pqor_id . '" style="display: none;">' .
                        VarDumper::dumpAsString(JsonHelper::decode($model->pqor_data_json), 10, true) . '</pre>' : '-';

                    return $content;
                },
                'format' => 'raw',
                'contentOptions' => [
                    'style' => ['max-width' => '800px', 'word-wrap' => 'break-word !important'],
                ],
            ],
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
