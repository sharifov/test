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
/* @var $isAgent bool */

$this->title = 'Search Cases';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cases-index">
    <h1><i class=""></i> <?= Html::encode($this->title) ?></h1>

    <div class="">
        <div class="x_panel">
            <div class="x_title">
                <h2><i class="fa fa-search"></i> Search</h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li>
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="display: block">
                <?php
                if ($isAgent) {
                    $searchTpl = '_search_agents';
                } else {
                    $searchTpl = '_search';
                }
                ?>
                <?= $this->render($searchTpl, ['model' => $searchModel]); ?>
            </div>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'cs_id',
                'options' => [
                    'style' => 'width:80px'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
            'cs_gid',
            [
                'attribute' => 'cs_project_id',
                'value' => function (Cases $model) {
                    return $model->project ? '<span class="badge badge-info">' . Html::encode($model->project->name) . '</span>' : '-';
                },
                'format' => 'raw',
                'filter' => Project::getList()
            ],
            [
                'attribute' => 'cs_dep_id',
                'value' => function (Cases $model) {
                    return $model->department ? $model->department->dep_name : '';
                },
                'filter' => Department::getList()
            ],
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
            'cs_subject',
            [
                'attribute' => 'cs_user_id',
                'value' => function (Cases $model) {
                    return $model->owner ? '<i class="fa fa-user"></i> ' .Html::encode($model->owner->username) : '-';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'cs_lead_id',
                'value' => function (Cases $model) {
                    return $model->lead ? $model->lead->uid : '-';
                },
            ],

            [
                'attribute' => 'cs_created_dt',
                'value' => function (Cases $model) {
                    return $model->cs_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->cs_created_dt)) : '-';
                },
                'format' => 'raw'
            ],

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
