<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLog;

/* @var $this yii\web\View */
/* @var $searchModel src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Business Extra Queue Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-business-extra-queue-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Business Extra Queue Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'lbeql_id',
            'lbeql_lbeqr_id',
            'lbeql_lead_id',
            'lbeql_status',
            'lbeql_lead_owner_id',
            'lbeql_created_dt',
            'lbeql_updated_dt',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, LeadBusinessExtraQueueLog $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'lbeql_id' => $model->lbeql_id]);
                }
            ],
        ],
    ]); ?>


</div>
