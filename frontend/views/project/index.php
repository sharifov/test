<?php

use common\components\grid\Select2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Projects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Project', ['create'], ['class' => 'btn btn-success']) ?>

        <?= Html::a('<i class="fa fa-refresh"></i> Synchronization Projects from BO', ['synchronization'], ['class' => 'btn btn-warning', 'data' => [
            'confirm' => 'Are you sure you want synchronization all projects from BackOffice Server?',
            'method' => 'post',
            'tooltip'
        ],]) ?>

    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-hover'],
        'rowOptions' => static function (\common\models\Project $model) {
            if ($model->closed) {
                return [
                    'class' => 'danger'
                ];
            }
        },
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [

            ['attribute' => 'id',
                'headerOptions' => ['style' => 'width:70px'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',

                'headerOptions' => ['style' => 'width:70px'],
                'template' => '{view} {update} {sources}',
                'buttons' => [
                    'sources' => function ($url, \common\models\Project $model, $key) {
                        return Html::a('<span class="fa fa-list text-info"></span>', ['sources/index', 'SourcesSearch[project_id]' => $model->id], ['title' => 'Sources', 'target' => '_blank', 'data-pjax' => 0]);
                    },
//                    'settings' => function ($url, \common\models\Project $model, $key) {
//                        return Html::a('<span class="fa fa-cog"></span>', ['settings/projects', 'id' => $model->id], ['title' => 'Settings', 'target' => '_blank', 'data-pjax' => 0]);
//                    },
                    /*'switch' => function ($url, \common\models\Employee $model, $key) {
                        return Html::a('<span class="fa fa-sign-in"></span>', ['employee/switch', 'id' => $model->id], ['title' => 'switch User', 'data' => [
                            'confirm' => 'Are you sure you want to switch user?',
                            //'method' => 'get',
                        ],]);
                    },*/
                ]
            ],
            [
                'attribute' => 'project_key',
                'value' => static function (\common\models\Project $model) {
                    return '<span class="badge badge-primary">' . $model->project_key . '</span>';
                },
                'format' => 'raw'
            ],
            //'project_key',
            'name:projectName',
            [
                'class' => Select2Column::class,
                'attribute' => 'related_projects',
                'format' => 'raw',
                'value' => static function (\common\models\Project $project) {
                    if (!$project->projectRelations) {
                        return Yii::$app->formatter->nullDisplay;
                    }
                    $result = [];
                    foreach ($project->projectRelations as $key => $value) {
                        $result[] = '<span>' . Yii::$app->formatter->asProjectName($value->prl_related_project_id) . '</span>';
                    }
                    return implode(' ', $result);
                },
                'data' => \common\models\Project::getList(),
                'id' => 'related_projects-filter',
                'options' => ['width' => '200px', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true, '']
            ],
            [
                'label' => 'Sources',
                'value' => static function (\common\models\Project $model) {
                    return $model->sources ? Html::a(count($model->sources), ['sources/index', 'SourcesSearch[project_id]' => $model->id], ['title' => 'Sources', 'target' => '_blank', 'data-pjax' => 0]) : '-';
                },
                'format' => 'raw'
            ],
            'link:url',
            'email_postfix',
            'closed:boolean',
            'sort_order',
            [
                'attribute' => 'contact_info',
                'format' => 'raw',
                'value' => static function (\common\models\Project $model) {
                    $resultStr = '-';
                    if ($decodedData = @json_decode($model->contact_info, true, 512, JSON_THROW_ON_ERROR)) {
                        $truncatedStr = StringHelper::truncate(
                            Html::encode(VarDumper::dumpAsString($decodedData)),
                            1200,
                            '...',
                            null,
                            false
                        );

                        $detailData = VarDumper::dumpAsString($decodedData, 10, true);
                        $detailBox = '<div id="detail_' . $model->id . '" style="display: none;">' . $detailData . '</div>';
                        $detailBtn = ' <i class="fas fa-eye green showDetail" style="cursor: pointer;" data-idt="' . $model->id . '"></i>';

                        $resultStr = $truncatedStr . $detailBox . $detailBtn;
                    }
                    return '<small>' . $resultStr . '</small>';
                },
            ],
            [
                'attribute' => 'p_update_user_id',
                'value' => static function (\common\models\Project $model) {
                    return $model->pUpdateUser ? $model->pUpdateUser->username : ' - ';
                },
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'last_update'
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?php
yii\bootstrap4\Modal::begin([
    'title' => 'Log detail',
    'id' => 'modal',
    'size' => \yii\bootstrap4\Modal::SIZE_LARGE,
]);
yii\bootstrap4\Modal::end();

$jsCode = <<<JS
    $(document).on('click', '.showDetail', function(){
        
        let logId = $(this).data('idt');
        let detailEl = $('#detail_' + logId);
        let modalBodyEl = $('#modal .modal-body');
        
        modalBodyEl.html(detailEl.html()); 
        $('#modal-label').html('Detail Api Log (' + logId + ')');       
        $('#modal').modal('show');
        return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);
