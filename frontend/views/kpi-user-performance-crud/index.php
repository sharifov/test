<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\MonthColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel \sales\model\kpi\entity\kpiUserPerformance\search\KpiUserPerformanceSearch */
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
                'class' => UserSelect2Column::class,
                'attribute' => 'up_user_id',
                'relation' => 'upUser',
                'placeholder' => 'Select User',
            ],

            'up_year',
			[
				'class' => MonthColumn::class,
				'attribute' => 'up_month',
			],
            'up_performance:percentInteger',

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'up_created_user_id',
                'relation' => 'upCreatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'up_updated_user_id',
                'relation' => 'upUpdatedUser',
                'placeholder' => 'Select User',
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
