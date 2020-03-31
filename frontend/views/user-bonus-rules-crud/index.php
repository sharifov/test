<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserBonusRulesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Bonus Rules';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-bonus-rules-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create User Bonus Rules', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ubr_exp_month',
            'ubr_kpi_percent:percentInteger',
            'ubr_order_profit',
            'ubr_value',
			[
				'class' => \common\components\grid\UserSelect2Column::class,
				'attribute' => 'ubr_created_user_id',
				'relation' => 'ubrCreatedUser',
				'url' => '/employee/list-ajax',
				'headerOptions' => ['style' => 'width:13%'],
			],
			[
				'class' => \common\components\grid\UserSelect2Column::class,
				'attribute' => 'ubr_updated_user_id',
				'relation' => 'ubrUpdatedUser',
				'url' => '/employee/list-ajax',
				'headerOptions' => ['style' => 'width:13%'],
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'ubr_created_dt',
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'ubr_updated_dt',
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
