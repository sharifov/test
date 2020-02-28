<?php

use modules\product\src\grid\columns\ProductTypeColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\MonthColumn;
use sales\yii\grid\UserColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\kpi\entity\kpiUserProductCommission\search\KpiUserProductCommissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Kpi User Product Commissions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kpi-user-product-commission-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Kpi User Product Commission', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

			[
				'class' => ProductTypeColumn::class,
				'attribute' => 'upc_product_type_id',
			],
            [
				'class' => UserColumn::class,
				'attribute' => 'upc_user_id',
				'relation' => 'upcUser'
			],
            'upc_year',
			[
				'class' => MonthColumn::class,
				'attribute' => 'upc_month',
			],
            'upc_performance:percentInteger',
            'upc_commission_percent:percentInteger',
			[
				'class' => UserColumn::class,
				'attribute' => 'upc_created_user_id',
				'relation' => 'upcCreatedUser'
			],
			[
				'class' => UserColumn::class,
				'attribute' => 'upc_updated_user_id',
				'relation' => 'upcCreatedUser'
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'upc_created_dt',
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'upc_updated_dt',
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
