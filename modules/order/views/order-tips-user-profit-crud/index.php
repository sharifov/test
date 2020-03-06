<?php

use modules\order\src\grid\columns\OrderColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel modules\order\src\entities\orderTipsUserProfit\search\OrderTipsUserProfitSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Tips User Profits';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-tips-user-profit-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Order Tips User Profit', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

			[
				'class' => OrderColumn::class,
				'attribute' => 'otup_order_id',
				'relation' => 'otupOrder',
			],
			[
				'class' => UserColumn::class,
				'attribute' => 'otup_user_id',
				'relation' => 'otupUser'
			],
            'otup_percent:percentInteger',
            'otup_amount',
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'otup_created_dt',
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'otup_updated_dt',
			],
			[
				'class' => UserColumn::class,
				'attribute' => 'otup_created_user_id',
				'relation' => 'otupCreatedUser'
			],
			[
				'class' => UserColumn::class,
				'attribute' => 'otup_updated_user_id',
				'relation' => 'otupCreatedUser'
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
