<?php

use modules\lead\src\grid\columns\LeadColumn;
use modules\order\src\grid\columns\OrderPayStatusColumn;
use modules\order\src\grid\columns\OrderStatusColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel \modules\order\src\entities\order\search\OrderCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'or_id',
            'or_gid',
            'or_uid',
            'or_name',
            [
                'class' => LeadColumn::class,
                'attribute' => 'or_lead_id',
                'relation' => 'orLead',
            ],
            'or_description:ntext',
            [
                'class' => OrderStatusColumn::class,
                'attribute' => 'or_status_id'
            ],
            [
                'class' => OrderPayStatusColumn::class,
                'attribute' => 'or_pay_status_id'
            ],
            'or_app_total',
            'or_app_markup',
            'or_agent_markup',
            'or_client_total',
            'or_client_currency',
            'or_client_currency_rate',
            [
                'class' => UserColumn::class,
                'attribute' => 'or_owner_user_id',
                'relation' => 'orOwnerUser',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'or_created_user_id',
                'relation' => 'orCreatedUser',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'or_updated_user_id',
                'relation' => 'orUpdatedUser',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'or_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'or_updated_dt',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
