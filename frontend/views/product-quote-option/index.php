<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProductQuoteOptionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Quote Options';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-option-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Quote Option', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'pqo_id',
            //'pqo_product_quote_id',
            [
                'attribute' => 'pqo_product_quote_id',
                'value' => static function(\common\models\ProductQuoteOption $model) {
                    return Html::a($model->pqo_product_quote_id, ['product-quote/view', 'id' => $model->pqo_product_quote_id], ['data-pjax' => 0, 'target' => '_blank']);
                },
                'format' => 'raw',

            ],
            //'pqo_product_option_id',
            [
                'attribute' => 'pqo_product_option_id',
                'value' => static function(\common\models\ProductQuoteOption $model) {
                    return $model->statusName;
                },
                'format' => 'raw',
                'filter' => \common\models\ProductOption::getList()
            ],
            'pqo_name',
            'pqo_description:ntext',
            //'pqo_status_id',
            [
                'attribute' => 'pqo_status_id',
                'value' => static function(\common\models\ProductQuoteOption $model) {
                    return $model->statusName;
                },
                'format' => 'raw',
                'filter' => \common\models\ProductQuoteOption::getStatusList()
            ],
            'pqo_price',
            'pqo_client_price',
            'pqo_extra_markup',
            'pqo_created_user_id',
            'pqo_updated_user_id',
//            'pqo_created_dt',
//            'pqo_updated_dt',

            [
                'attribute' => 'pqo_created_dt',
                'value' => static function(\common\models\ProductQuoteOption $model) {
                    return $model->pqo_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->pqo_created_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'pqo_updated_dt',
                'value' => static function(\common\models\ProductQuoteOption $model) {
                    return $model->pqo_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->pqo_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
