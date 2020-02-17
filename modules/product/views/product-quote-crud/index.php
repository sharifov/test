<?php

use modules\product\src\grid\columns\ProductColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel \modules\product\src\entities\productQuote\search\ProductQuoteCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Quotes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Quote', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'pq_id',
            'pq_gid',
            [
                'class' => modules\product\src\grid\columns\ProductQuoteColumn::class,
                'attribute' => 'pq_clone_id',
                'relation' => 'clone',
            ],
            'pq_name',
            [
                'class' => ProductColumn::class,
                'attribute' => 'pq_product_id',
                'relation' => 'pqProduct',
            ],
            'pq_order_id',
//            'pq_description:ntext',
            [
                'class' => \modules\product\src\grid\columns\ProductQuoteStatusColumn::class,
                'attribute' => 'pq_status_id',
            ],

            'pq_service_fee_sum',
            'pq_origin_price',
            'pq_origin_currency',
            'pq_origin_currency_rate',
            'pq_price',
            'pq_client_currency',
            'pq_client_currency_rate',
            'pq_profit_amount',
            'pq_client_price',
            [
                'label' => 'Calc client price',
                'value' => static function (\modules\product\src\entities\productQuote\ProductQuote $model) {
                    return number_format($model->pq_price * $model->pq_client_currency_rate, 2);
                }
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'pq_owner_user_id',
                'relation' => 'pqOwnerUser',
            ],
//            [
//                'class' => UserColumn::class,
//                'attribute' => 'pq_created_user_id',
//                'relation' => 'pqCreatedUser',
//            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'pq_updated_user_id',
                'relation' => 'pqUpdatedUser',
            ],
//            [
//                'class' => DateTimeColumn::class,
//                'attribute' => 'pq_created_dt',
//            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pq_updated_dt',
            ],
            ['class' => ActionColumn::class],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
