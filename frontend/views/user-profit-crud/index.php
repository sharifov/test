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
		'rowOptions'=> static function(UserProfit $model){
            return ['class' => $model->getRowClass()];
		},
        'columns' => [
            'up_id',
			[
				'class' => UserColumn::class,
				'attribute' => 'up_user_id',
				'relation' => 'upUser',
			],
            'upLead:lead:Lead',
            'upOrder:order:Order',
            'upProductQuote:productQuote:Product Quote',
            'up_percent:percent',
            'up_profit',
            'up_split_percent:percent',
            'up_amount',
            [
                'attribute' => 'up_status_id',
                'value' => static function (UserProfit $model) {
                    return UserProfit::asFormat($model->up_status_id);
                },
                'filter' => UserProfit::getStatusList(),
                'format' => 'raw'
            ],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'up_created_dt',
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'up_updated_dt',
			],
            'up_payroll_id:payroll',
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
