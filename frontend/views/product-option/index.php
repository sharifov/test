<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProductOptionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Options';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-option-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Option', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'po_id',
            'po_key',
            [
                'attribute' => 'po_product_type_id',
                'value' => static function(\common\models\ProductOption $model) {
                    return $model->poProductType ? $model->poProductType->pt_name : '-';
                },
                'filter' => \common\models\ProductType::getList()
            ],
            'po_name',
            //'po_description:ntext',
            //'po_price_type_id',
            [
                'attribute' => 'po_price_type_id',
                'value' => static function(\common\models\ProductOption $model) {
                    return $model->priceTypeLabel;
                },
                'format' => 'raw',
                'filter' => \common\models\ProductOption::getPriceTypeList()
            ],
            'po_max_price',
            'po_min_price',
            'po_price',
            'po_enabled',
            'po_created_user_id',
            'po_updated_user_id',
            [
                'attribute' => 'po_created_dt',
                'value' => static function(\common\models\ProductOption $model) {
                    return $model->po_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->po_created_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'po_updated_dt',
                'value' => static function(\common\models\ProductOption $model) {
                    return $model->po_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->po_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],
            //'po_created_dt',
            //'po_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
