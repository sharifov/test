<?php

use common\models\Department;
use common\models\Project;
use sales\entities\cases\CasesCategory;
use yii\helpers\Html;
use yii\grid\GridView;
use sales\entities\cases\Cases;

/* @var $this yii\web\View */
/* @var $searchModel sales\entities\cases\CasesQSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cases Trash';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .dropdown-menu {
        z-index: 1010 !important;
    }
</style>
<h1>
    <?= Html::encode($this->title) ?>
</h1>

<div class="cases-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cs_gid',
            [
                'attribute' => 'cs_project_id',
                'value' => function (Cases $model) {
                    return $model->project ? $model->project->name : '';
                },
//                'filter' => Project::getList()
            ],
            'cs_subject',
            [
                'attribute' => 'cs_category',
                'value' => function (Cases $model) {
                    return $model->category ? $model->category->cc_name : '';
                },
//                'filter' => CasesCategory::getList()
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
//                'filter' => Department::getList()
            ],
            'cs_created_dt',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, Cases $model) {
                        return Html::a('<i class="glyphicon glyphicon-search"></i> View Case', [
                            'cases/view',
                            'gid' => $model->cs_gid
                        ], [
                            'class' => 'btn btn-info btn-xs',
                            'target' => '_blank',
                            'data-pjax' => 0,
                            'title' => 'View',
                        ]);
                    },
                ],
            ]

        ],
    ]); ?>

</div>
