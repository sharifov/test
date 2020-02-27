<?php

use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\MonthColumn;
use sales\yii\grid\UserColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\kpi\entity\search\KpiUserPerformanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Kpi User Performances';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kpi-user-performance-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Kpi User Performance', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
			[
				'class' => UserColumn::class,
				'attribute' => 'up_user_id',
				'relation' => 'upUser'
			],
            'up_year',
			[
				'class' => MonthColumn::class,
				'attribute' => 'up_month',
			],
            'up_performance',
            'up_created_user_id',
			[
				'class' => UserColumn::class,
				'attribute' => 'up_created_user_id',
				'relation' => 'upCreatedUser'
			],
			[
				'class' => UserColumn::class,
				'attribute' => 'up_updated_user_id',
				'relation' => 'upUpdatedUser'
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'up_created_dt',
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'up_updated_dt',
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
