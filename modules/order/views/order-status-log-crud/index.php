<?php

use modules\order\src\grid\columns\OrderColumn;
use modules\order\src\grid\columns\OrderStatusActionColumn;
use modules\order\src\grid\columns\OrderStatusColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\DurationColumn;
use common\components\grid\UserColumn;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\order\src\entities\orderStatusLog\search\OrderStatusLogCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Status Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-status-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create order Status Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'orsl_id',
            [
                'class' => OrderColumn::class,
                'attribute' => 'orsl_order_id',
                'relation' => 'order',
            ],
            [
                'class' => OrderStatusColumn::class,
                'attribute' => 'orsl_start_status_id',
            ],
            [
                'class' => OrderStatusColumn::class,
                'attribute' => 'orsl_end_status_id',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'orsl_start_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'orsl_end_dt',
            ],
            [
                'class' => DurationColumn::class,
                'attribute' => 'orsl_duration',
                'startAttribute' => 'orsl_start_dt',
            ],
            'orsl_description',
            [
                'class' => OrderStatusActionColumn::class,
                'attribute' => 'orsl_action_id'
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'orsl_owner_user_id',
                'relation' => 'ownerUser',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'orsl_created_user_id',
                'relation' => 'createdUser',
            ],
            ['class' => ActionColumn::class],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
