<?php

use modules\lead\src\grid\columns\LeadColumn;
use modules\product\src\grid\columns\ProductTypeColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
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

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'pr_id',
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
            'pr_status_id',
            'pr_service_fee_percent',
            'pr_market_price',
            'pr_client_budget',
            [
                'class' => UserColumn::class,
                'attribute' => 'pr_created_user_id',
                'relation' => 'prCreatedUser',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'pr_updated_user_id',
                'relation' => 'prUpdatedUser',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pr_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pr_updated_dt',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
