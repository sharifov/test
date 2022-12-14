<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;
use src\model\callLog\entity\callLog\search\CallLogSearch;
use common\models\Project;

/**
 * @var $callLogDataProvider yii\data\ActiveDataProvider
 */
?>

<?php Pjax::begin(['timeout' => 5000]); ?>
<?php /* echo $this->render('_info_calls_search', ['model' => $callLogSearchModel]); */ ?>
<h5>Calls Stats</h5>
<div class="well">
    <?= GridView::widget([
        'dataProvider' => $callLogDataProvider,
        'filterModel' => $callLogSearchModel,
        'emptyTextOptions' => [
            'class' => 'text-center'
        ],
        'columns' => [
            'cl_id',
            [
                'label' => 'Date/Time',
                'class' => DateTimeColumn::class,
                'attribute' => 'cl_call_created_dt',
                'format' => 'byUserDateTimeWithSeconds'],
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'cl_project_id',
                'relation' => 'project',
                'filter' => Project::getListByUser(Yii::$app->user->id),
                'visible' => count(Project::getList()) > 1
            ],
            [
                'class' => \common\components\grid\department\DepartmentColumn::class,
                'attribute' => 'cl_department_id',
                'relation' => 'department',
            ],
            ['class' => \src\model\callLog\grid\columns\CallLogTypeColumn::class],
            ['class' => \src\model\callLog\grid\columns\CallLogCategoryColumn::class],
            ['class' => \src\model\callLog\grid\columns\CallLogStatusColumn::class],
            [
                'label' => 'From',
                'attribute' => 'cl_phone_from',
            ],
            [
                'label' => 'To',
                'attribute' => 'cl_phone_to',
            ],
            [
                'attribute' => 'cl_duration',
                'value' => static function (CallLogSearch $log) {
                    if ($log->cl_duration && $log->cl_duration >= 3600) {
                        $format = 'H:i:s';
                    } else {
                        $format = 'i:s';
                    }
                    return $log->cl_duration ? '<i class="fa fa-clock-o"></i> <span title="' . Yii::$app->formatter->asDuration($log->cl_duration) . '">' . gmdate($format, $log->cl_duration) . '</span>' : null;
                },
                'format' => 'raw',
                'filter' => false
            ],
            [
                'class' => \src\model\callLog\grid\columns\RecordingUrlColumn::class,
                'filter' => false
            ],
            [
                'label' => 'Contact',
                'attribute' => 'cl_client_id',
                'format' => 'client'
            ],
            /*[
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
            ],*/
            /*[
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
            ]*/
        ]
    ]); ?>
</div>
<?php Pjax::end(); ?>
