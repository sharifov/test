<?php

use sales\model\callLog\entity\callLogQueue\CallLogQueue;
use sales\yii\grid\BooleanColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\callLog\entity\callLogQueue\search\CallLogQueueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call Log Queues';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-log-queue-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Call Log Queue', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'clq_cl_id:callLog',
            'clq_queue_time:datetime',
            'clq_access_count',
            ['class' => BooleanColumn::class, 'attribute' => 'clq_is_transfer'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
