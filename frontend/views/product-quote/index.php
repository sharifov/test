<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProductQuoteSearch */
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
            //['class' => 'yii\grid\SerialColumn'],

            'pq_id',
            'pq_gid',
            'pq_name',
            'pq_product_id',
            'pq_order_id',
            'pq_description:ntext',
            'pq_status_id',
            'pq_price',
            'pq_origin_price',
            'pq_client_price',
            'pq_service_fee_sum',
            'pq_origin_currency',
            'pq_client_currency',
            'pq_origin_currency_rate',
            'pq_client_currency_rate',
            'pq_owner_user_id',
            'pq_created_user_id',
            'pq_updated_user_id',
            [
                'attribute' => 'pq_created_dt',
                'value' => static function(\common\models\ProductQuote $model) {
                    return $model->pq_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->pq_created_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'pq_updated_dt',
                'value' => static function(\common\models\ProductQuote $model) {
                    return $model->pq_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->pq_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],
//            'pq_created_dt',
//            'pq_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
