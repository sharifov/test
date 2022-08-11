<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLog;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogStatus;
use src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRuleQuery;

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
            [
                'attribute' => 'lbeql_id',
                'label' => 'ID',
                'options' => [
                    'style' => 'width:80px'
                ]
            ],
            [
                'attribute' => 'lbeql_lbeqr_id',
                'value' => static function (LeadBusinessExtraQueueLog $model) {
                    return '<i class="fa fa-key"></i> ' . $model->lbeqlLbeqr->lbeqr_key;
                },
                'filter' => LeadBusinessExtraQueueRuleQuery::getList(60),
                'format' => 'raw',
            ],
            [
                'attribute' => 'lbeql_lead_id',
                'value' => static function (LeadBusinessExtraQueueLog $model) {
                    return Yii::$app->formatter->asLead($model->lbeqlLead, 'fa-cubes');
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'lbeql_status',
                'filter'  => LeadBusinessExtraQueueLogStatus::STATUS_LIST,
                'value' => static function (LeadBusinessExtraQueueLog $model) {
                    return $model->getStatusName();
                },
            ],
            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'lbeql_lead_owner_id',
                'relation' => 'owner',
                'placeholder' => 'Lead Owner'
            ],
            'lbeql_description',
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'lbeql_created_dt',
                'limitEndDay' => false,
            ],
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'lbeql_updated_dt',
                'limitEndDay' => false,
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, LeadBusinessExtraQueueLog $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'lbeql_id' => $model->lbeql_id]);
                }
            ],
        ],
    ]); ?>


</div>
