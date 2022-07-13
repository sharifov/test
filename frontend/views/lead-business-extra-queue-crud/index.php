<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use src\model\leadBusinessExtraQueue\entity\LeadBusinessExtraQueue;

/* @var $this yii\web\View */
/* @var $searchModel src\model\leadBusinessExtraQueue\entity\LeadBusinessExtraQueueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Business Extra Queues';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-business-extra-queue-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Business Extra Queue', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'lbeq_lead_id',
            'lbeq_lbeqr_id',
            'lbeq_created_dt',
            'lbeq_expiration_dt',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, LeadBusinessExtraQueue $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'lbeq_lead_id' => $model->lbeq_lead_id, 'lbeq_lbeqr_id' => $model->lbeq_lbeqr_id]);
                }
            ],
        ],
    ]); ?>


</div>
