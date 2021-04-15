<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use modules\order\src\entities\order\Order;

/**
 * @var $searchModel \modules\order\src\entities\order\search\OrderQSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = 'New';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1>
    <i class="fa fa-paste text-warning"></i> <?= Html::encode($this->title) ?>
</h1>

<div class="orders-q-new">
    <?php Pjax::begin(['id' => 'orders-q-new-pjax-list', 'timeout' => 5000, 'enablePushState' => true]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'or_id',
            ],
            'or_fare_id',
            'or_project_id',
            'or_created_dt',
            [
                'label' => 'Booking ID',
                'attribute' => 'or_uid'
            ],
        ]
    ])
?>

    <?php Pjax::end() ?>
</div>
