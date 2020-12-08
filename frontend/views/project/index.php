<?php

use yii\helpers\Html;
use yii\grid\GridView;
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
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

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
                'label' => 'Sources',
                'value' => static function (\common\models\Project $model) {
                    return $model->sources ? Html::a(count($model->sources), ['sources/index', 'SourcesSearch[project_id]' => $model->id], ['title' => 'Sources', 'target' => '_blank', 'data-pjax' => 0]) : '-';
                },
                'format' => 'raw'
            ],
            //'link',

            [
                'attribute' => 'link',
                'value' => static function (\common\models\Project $model) {
                    return Html::a($model->link, $model->link, ['target' => '_blank']);
                },
                'format' => 'raw'
            ],


            //'api_key',
            'email_postfix',
            'closed:boolean',
            'sort_order',
            //'api_key',
            //'contact_info:ntext',
            [
                'attribute' => 'contact_info',
                'value' => static function (\common\models\Project $model) {
                    return \yii\helpers\VarDumper::dumpAsString($model->contactInfo->attributes, 5);
                },
                //'format' => 'raw'
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'last_update'
            ],

            /*[
                'attribute' => 'last_update',
                'value' => static function (\common\models\Project $model) {
                    return $model->last_update ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->last_update)) : '-';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'last_update',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],*/

            //'custom_data:ntext',


        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
