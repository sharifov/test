<?php

use modules\order\src\grid\columns\OrderColumn;
use sales\yii\grid\DateTimeColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel modules\order\src\entities\orderTips\search\OrderTipsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Tips';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-tips-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Order Tips', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'ot_id',
			[
				'class' => OrderColumn::class,
				'attribute' => 'ot_order_id',
				'relation' => 'otOrder',
			],
            'ot_client_amount',
            'ot_amount',
            'ot_user_profit',
			'ot_user_profit_percent:percentInteger',
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'ot_created_dt',
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'ot_updated_dt',
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
