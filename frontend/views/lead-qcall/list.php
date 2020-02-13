<?php

use sales\formatters\client\ClientTimeFormatter;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadQcallSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead QCall List';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-qcall-list">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php //= Html::a('Create Lead Qcall', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_filter', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'lqc_lead_id',
            [
                'attribute' => 'lqc_lead_id',
                'value' => static function (\common\models\LeadQcall $model) {
                    return Html::a($model->lqc_lead_id, ['lead/view', 'gid' => $model->lqcLead->gid], ['target' => '_blank', 'data-pjax' => 0]);
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'lqcLead.project_id',
                'value' => static function (\common\models\LeadQcall $model) {
                    return $model->lqcLead->project ? '<span class="badge badge-info">' . Html::encode($model->lqcLead->project->name) . '</span>' : '-';
                },
                'format' => 'raw',
                'options' => [
                    'style' => 'width:120px'
                ],
                'filter' => \common\models\Project::getList(),
            ],

            [
                'attribute' => 'lqcLead.source_id',
                'value' => function(\common\models\LeadQcall $model) {
                    return $model->lqcLead->source ? $model->lqcLead->source->name : '-';
                },
                'filter' => \common\models\Sources::getList(true)
            ],

            [
                'header' => 'Client time',
                'format' => 'raw',
                'value' => function(\common\models\LeadQcall $model) {
                    return ClientTimeFormatter::format($model->lqcLead->getClientTime2(), $model->lqcLead->offset_gmt);
                },
                'options' => ['style' => 'width:90px'],
            ],

            [
                'attribute' => 'employee_id',
                'format' => 'raw',
                'value' => static function (\common\models\LeadQcall $model) {
                    return $model->lqcLead->employee ? '<i class="fa fa-user"></i> ' . $model->lqcLead->employee->username : '-';
                },
                'filter' => false //\common\models\Employee::getList()
            ],

            [
                'attribute' => 'lqcLead.pending',
                'label' => 'Pending Time',
                'value' => static function (\common\models\LeadQcall $model) {

                    $createdTS = strtotime($model->lqcLead->created);

                    $diffTime = time() - $createdTS;
                    $diffHours = (int) ($diffTime / (60 * 60));


                    $str = ($diffHours > 3 && $diffHours < 73 ) ? $diffHours.' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                    $str .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lqcLead->created));

                    return $str;
                },
                'options' => [
                    'style' => 'width:160px'
                ],
                'format' => 'raw'
            ],

            [
                'label' => 'Out Calls',
                'value' => static function (\common\models\LeadQcall $model) {
                    $cnt = $model->lqcLead->getCountCalls(\common\models\Call::CALL_TYPE_OUT);
                    return $cnt ?: '-';
                },
                'options' => [
                    'style' => 'width:60px'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                //'format' => 'raw'
            ],


            'lqc_weight',
            [
                'attribute' => 'lqc_dt_from',
                'value' => static function (\common\models\LeadQcall $model) {
                    return $model->lqc_dt_from ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lqc_dt_from)) : '-';
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'lqc_dt_to',
                'value' => static function (\common\models\LeadQcall $model) {
                    return $model->lqc_dt_to ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lqc_dt_to)) : '-';
                },
                'format' => 'raw'
            ],

            [
                'label' => 'Duration',
                'value' => static function (\common\models\LeadQcall $model) {
                    return Yii::$app->formatter->asDuration(strtotime($model->lqc_dt_to) - strtotime($model->lqc_dt_from));
                },
            ],

            [
                'label' => 'Deadline',
                'value' => static function (\common\models\LeadQcall $model) {
                    $timeTo = strtotime($model->lqc_dt_to);
                    return time() <= $timeTo ? Yii::$app->formatter->asDuration($timeTo - time()) : 'deadline';
                },
            ],


            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
