<?php

use modules\product\src\grid\columns\ProductTypeColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\kpi\entity\kpiProductCommission\search\KpiProductCommissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Kpi Product Commissions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kpi-product-commission-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Kpi Product Commission', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
			[
				'class' => ProductTypeColumn::class,
				'attribute' => 'pc_product_type_id',
			],
            'pc_performance:percentInteger',
            'pc_commission_percent:percentInteger',

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'pc_created_user_id',
                'relation' => 'pcCreatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'pc_updated_user_id',
                'relation' => 'pcUpdatedUser',
                'placeholder' => 'Select User',
            ],

			[
				'class' => DateTimeColumn::class,
				'attribute' => 'pc_created_dt',
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'pc_updated_dt',
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
