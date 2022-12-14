<?php

use yii\grid\ActionColumn;
use modules\lead\src\grid\columns\LeadColumn;
use modules\product\src\grid\columns\ProductTypeColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel \modules\product\src\entities\product\search\ProductCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'pr_id',
            'pr_gid',
            [
                'attribute' => 'pr_type_id',
                'class' => ProductTypeColumn::class,
            ],
            'pr_name',
            [
                'class' => LeadColumn::class,
                'attribute' => 'pr_lead_id',
                'relation' => 'prLead',
            ],
            'pr_description:ntext',
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'pr_project_id',
                'relation' => 'project',
            ],
            //'pr_status_id',
            'pr_service_fee_percent',
            'pr_market_price',
            'pr_client_budget',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'pr_created_user_id',
                'relation' => 'prCreatedUser',
                'placeholder' => 'Select User',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pr_created_dt',
            ],
            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
