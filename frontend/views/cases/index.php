<?php

use common\models\Department;
use common\models\Project;
use sales\entities\cases\CasesCategory;
use yii\helpers\Html;
use yii\grid\GridView;
use sales\entities\cases\Cases;
use \sales\entities\cases\CasesStatus;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel sales\entities\cases\CasesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cases';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cases-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cs_id',
            'cs_gid',
            [
                'attribute' => 'cs_project_id',
                'value' => function (Cases $model) {
                    return $model->project ? $model->project->name : '';
                },
                'filter' => Project::getList()
            ],
            'cs_subject',
            [
                'attribute' => 'cs_category',
                'value' => function (Cases $model) {
                    return $model->category ? $model->category->cc_name : '';
                },
                'filter' => CasesCategory::getList()
            ],
            [
                'attribute' => 'cs_status',
                'value' => function (Cases $model) {
                    $value = CasesStatus::getName($model->cs_status);
                    $str = '<span class="label ' . CasesStatus::getClass($model->cs_status) . '">' . $value . '</span>';
                    if ($model->lastLogRecord) {
                        $str .= '<br><br><span class="label label-default">' . Yii::$app->formatter->asDatetime(strtotime($model->lastLogRecord->csl_start_dt)) . '</span>';
                        $str .= '<br>';
                        $str .= $model->lastLogRecord ? Yii::$app->formatter->asRelativeTime(strtotime($model->lastLogRecord->csl_start_dt)) : '';
                    }
                    return $str;
                },
                'format' => 'raw',
                'filter' => CasesStatus::STATUS_LIST,
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
            [
                'attribute' => 'cs_user_id',
                'value' => function (Cases $model) {
                    return $model->owner ? $model->owner->username : '';
                },
            ],
            [
                'attribute' => 'cs_lead_id',
                'value' => function (Cases $model) {
                    return $model->lead ? $model->lead->uid : '';
                },
            ],
            [
                'attribute' => 'cs_dep_id',
                'value' => function (Cases $model) {
                    return $model->department ? $model->department->dep_name : '';
                },
                'filter' => Department::getList()
            ],
            'cs_created_dt',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
//                'visibleButtons' => [
//                    'view' => function ($model, $key, $index) {
//                        return Yii::$app->user->can('admin');
//                    },
//                ],
                'buttons' => [
                    'view' => function ($url, Cases $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to([
                            'cases/view',
                            'gid' => $model->cs_gid
                        ]));
                    }
                ]
            ]

        ],
    ]); ?>


</div>
