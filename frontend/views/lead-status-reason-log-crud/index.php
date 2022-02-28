<?php

use src\model\leadStatusReasonLog\entity\LeadStatusReasonLog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\leadStatusReasonLog\entity\LeadStatusReasonLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Status Reason Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-status-reason-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Status Reason Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'lsrl_id',
            [
                'attribute' => 'lsrl_lead_flow_id',
                'value' => static function (LeadStatusReasonLog $model) {
                    return Html::a('<i class="fa fa-link"></i> ' . $model->lsrl_lead_flow_id, ['/lead-flow/view', 'id' => $model->lsrl_lead_flow_id]);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'lsrl_lead_status_reason_id',
                'value' => static function (LeadStatusReasonLog $model) {
                    return Html::a('<i class="fa fa-link"></i> ' . $model->lsrl_lead_status_reason_id, ['/lead-status-reason-crud/view', 'lsr_id' => $model->lsrl_lead_status_reason_id]);
                },
                'format' => 'raw'
            ],
            'lsrl_comment',
            'lsrl_created_dt:byUserDateTime',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, LeadStatusReasonLog $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'lsrl_id' => $model->lsrl_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
