<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\components\grid\DateTimeColumn;
use \sales\model\callLog\entity\callLog\search\CallLogSearch;

/**
 * @var $searchModel sales\model\callLog\entity\callLog\search\CallLogSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = 'My Calls Log';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="call-log-list">
    <h1><i class="fa fa-phone"></i> <?= Html::encode($this->title) ?></h1>
</div>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'cl_id',
        [
            'class' => DateTimeColumn::class,
            'attribute' => 'cl_call_created_dt',
            'format' => 'byUserDateTimeWithSeconds'],
        [
            'class' => \common\components\grid\project\ProjectColumn::class,
            'attribute' => 'cl_project_id',
            'relation' => 'project',
        ],
        [
            'class' => \common\components\grid\department\DepartmentColumn::class,
            'attribute' => 'cl_department_id',
            'relation' => 'department',
        ],
        ['class' => \sales\model\callLog\grid\columns\CallLogTypeColumn::class],
        ['class' => \sales\model\callLog\grid\columns\CallLogCategoryColumn::class],
        ['class' => \sales\model\callLog\grid\columns\CallLogStatusColumn::class],
        'cl_phone_from',
        'cl_phone_to',
        [
            'attribute' => 'cl_duration',
            'value' => static function(CallLogSearch $log){
                if ($log->cl_duration && $log->cl_duration >= 3600) {
                    $format = 'H:i:s';
                } else {
                    $format = 'i:s';
                }
                return $log->cl_duration ? '<i class="fa fa-clock-o"></i> <span title="'.Yii::$app->formatter->asDuration($log->cl_duration).'">' . gmdate($format, $log->cl_duration) . '</span>' : null;
            },
            'format' => 'raw',
            'filter' => false
        ],
        [
            'class' => \sales\model\callLog\grid\columns\RecordingUrlColumn::class,
            'filter' => false
        ],
        'cl_client_id:client',
        [
            'class' => \common\components\grid\CombinedDataColumn::class,
            'labelTemplate' => '{0}  /  {1}',
            'valueTemplate' => '{0}  <br>  {1}',
            'attributes' => [
                'lead_id',
                'case_id',
            ],
            'values' => [
                static function (CallLogSearch $log) {
                    return $log->callLogLead ? $log->callLogLead->lead : null;
                },
                static function (CallLogSearch $log) {
                    return $log->callLogCase ? $log->callLogCase->case : null;
                },
            ],
            'formats' => [
                'lead',
                'case'
            ],
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            'buttons' => [
                'view' => static function ($url, CallLogSearch $model) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/call-log/view', 'id' => $model->cl_id], [
                        'target' => '_blank',
                        'data-pjax' => 0,
                        'title' => 'View',
                    ]);
                },
            ],
        ]
    ]
]); ?>
