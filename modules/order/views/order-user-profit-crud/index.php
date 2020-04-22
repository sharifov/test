<?php

use modules\order\src\entities\orderUserProfit\OrderUserProfit;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel modules\order\src\entities\orderUserProfit\search\OrderUserProfitSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order User Profits';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-user-profit-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Order User Profit', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'oup_order_id',
                'value' => static function (OrderUserProfit $orderUserProfit) {
                    return Html::a($orderUserProfit->oup_order_id, Url::toRoute(['/order/order-crud/view', 'id' => $orderUserProfit->oup_order_id]), [
						'target' => '_blank',
						'data-pjax' => 0
					]);
                },
                'format' => 'raw'
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'oup_user_id',
                'relation' => 'oupUser',
                'placeholder' => 'Select User',
            ],

            'oup_percent',
            'oup_amount',
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'oup_created_dt',
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'oup_updated_dt',
			],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'oup_created_user_id',
                'relation' => 'oupCreatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'oup_updated_user_id',
                'relation' => 'oupUpdatedUser',
                'placeholder' => 'Select User',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
