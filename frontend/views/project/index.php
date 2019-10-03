<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
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
        <?= Html::a('Create Project', ['create'], ['class' => 'btn btn-success']) ?>

        <?= Html::a('Synchronization Projects from BO', ['synchronization'], ['class' => 'btn btn-warning', 'data' => [
            'confirm' => 'Are you sure you want synchronization all projects from BackOffice Server?',
            'method' => 'post',
        ],]) ?>

    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            //'link',

            [
                'attribute' => 'link',
                'value' => function (\common\models\Project $model) {
                    return Html::a($model->link, $model->link, ['target' => '_blank']);
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'last_update',
                'value' => function (\common\models\Project $model) {
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
            ],
            //'api_key',
            'closed:boolean',

            //'api_key',
            'contact_info:ntext',

            //'last_update',
            'custom_data:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {sources} {settings}',
                'visibleButtons' => [
                    /*'view' => function ($model, $key, $index) {
                        return User::hasPermission('viewOrder');
                    },*/
                    /*'update' => function (\common\models\Employee $model, $key, $index) {
                        return (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || !in_array('admin', array_keys($model->getRoles())));
                    },
                    'projects' => function (\common\models\Employee $model, $key, $index) {
                        return (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || !in_array('admin', array_keys($model->getRoles())));
                    },
                    'groups' => function (\common\models\Employee $model, $key, $index) {
                        return (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || !in_array('admin', array_keys($model->getRoles())));
                    },
                    'switch' => function (\common\models\Employee $model, $key, $index) {
                        return (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || !in_array('admin', array_keys($model->getRoles())));
                    },*/
                ],
                'buttons' => [
                    'sources' => function ($url, \common\models\Project $model, $key) {
                        return Html::a('<span class="fa fa-list"></span>', ['sources/index', 'SourcesSearch[project_id]' => $model->id], ['title' => 'Sources'/*, 'target' => '_blank'*/]);
                    },
                    'settings' => function ($url, \common\models\Project $model, $key) {
                        return Html::a('<span class="fa fa-cog"></span>', ['settings/projects', 'id' => $model->id], ['title' => 'Settings'/*, 'target' => '_blank'*/]);
                    },
                    /*'switch' => function ($url, \common\models\Employee $model, $key) {
                        return Html::a('<span class="fa fa-sign-in"></span>', ['employee/switch', 'id' => $model->id], ['title' => 'switch User', 'data' => [
                            'confirm' => 'Are you sure you want to switch user?',
                            //'method' => 'get',
                        ],]);
                    },*/
                ]
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
