<?php

use sales\model\user\entity\profit\UserProfit;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\user\entity\profit\search\UserProfitSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Profits';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-profit-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Profit', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			[
				'class' => UserColumn::class,
				'attribute' => 'up_user_id',
				'relation' => 'upUser'
			],
            'up_lead_id',
            'up_order_id',
            'up_product_quote_id',
            'up_percent',
            'up_profit',
            'up_split_percent',
            'up_amount',
            [
                'attribute' => 'up_status_id',
                'value' => static function (UserProfit $model) {
                    return UserProfit::getStatusName($model->up_status_id);
                },
                'filter' => UserProfit::getStatusList()
            ],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'up_created_dt',
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'up_updated_dt',
			],
            'up_payroll_id',
            [
                'attribute' => 'up_type_id',
                'value' => static function (UserProfit $model) {
                    return UserProfit::getTypeName($model->up_type_id);
                },
                'filter' => UserProfit::getTypeList()
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
