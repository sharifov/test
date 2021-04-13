<?php

use common\components\grid\DateTimeColumn;
use common\models\Language;
use modules\order\src\entities\orderData\OrderData;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\order\src\entities\orderData\search\OrderDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Datas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Order Data', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-order-data']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'od_id',
            'od_order_id',
            'od_display_uid',
            'od_source_id:source',
            [
                'attribute' => 'od_language_id',
                'value' => static function (OrderData $model) {
                    return $model->od_language_id ? $model->language->name : null;
                },
                'filter' => Language::getList()
            ],
            'od_market_country',
            'od_created_by',
            'od_updated_by',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'od_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'od_updated_dt',
                'format' => 'byUserDateTime'
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
