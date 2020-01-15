<?php

use common\models\Department;
use common\models\Employee;
use common\models\Project;
use sales\access\ListsAccess;
use dosamigos\datepicker\DatePicker;
use sales\entities\cases\CasesCategory;
use yii\helpers\Html;
use yii\grid\GridView;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesQSearch;

/* @var $this yii\web\View */
/* @var $searchModel sales\entities\cases\CasesQSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Solved Queue';
$this->params['breadcrumbs'][] = $this->title;

/** @var Employee $user */
$user = Yii::$app->user->identity;

$lists = new ListsAccess($user->id);
?>
<h1>
    <i class="fa fa-flag"></i> <?= Html::encode($this->title) ?>
</h1>

<style>
    .dropdown-menu {
        z-index: 1010 !important;
    }
</style>

<div class="cases-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cs_id',
            'cs_gid',
            [
                'attribute' => 'cs_project_id',
                'value' => static function (CasesQSearch $model) {
                    return $model->project ? $model->project->name : '';
                },
                'filter' => Project::getList()
            ],
            'cs_subject',
            [
                'attribute' => 'cs_category',
                'value' => static function (CasesQSearch $model) {
                    return $model->category ? $model->category->cc_name : '';
                },
                'filter' => CasesCategory::getList()
            ],
            [
                'attribute' => 'cs_user_id',
                'label' => 'Agent',
                'value' => static function (CasesQSearch $model) {
                    return $model->owner ? $model->owner->username : '';
                },
                'filter' => $lists->getEmployees(),
                'visible' => $user->isSupSuper() || $user->isExSuper() || $user->isAdmin()
            ],
            [
                'attribute' => 'cs_lead_id',
                'value' => static function (CasesQSearch $model) {
                    return $model->lead ? $model->lead->uid : '';
                },
            ],
            [
                'attribute' => 'cs_dep_id',
                'value' => static function (CasesQSearch $model) {
                    return $model->department ? $model->department->dep_name : '';
                },
                'filter' => Department::getList()
            ],
            [
                'attribute' => 'cs_created_dt',
                'value' => static function (CasesQSearch $model) {
                    return $model->cs_created_dt ? Yii::$app->formatter->asDatetime(strtotime($model->cs_created_dt)) : '-';
                },
				'filter' => DatePicker::widget([
					'model' => $searchModel,
					'attribute' => 'cs_created_dt',
					'clientOptions' => [
						'autoclose' => true,
						'format' => 'yyyy-mm-dd'
					],
					'options' => [
						'autocomplete' => 'off'
					]
				]),
            ],
            [
                'attribute' => 'cs_last_action_dt',
                'label' => 'Last Action',
                'value' => static function (CasesQSearch $model) {
                    $createdTS = strtotime($model->cs_last_action_dt);

                    $diffTime = time() - $createdTS;
                    $diffHours = (int) ($diffTime / (60 * 60));

                    return ($diffHours > 3 && $diffHours < 73 ) ? $diffHours.' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                },
            ],
            [
                'attribute' => 'solved_date',
                'value' => static function (CasesQSearch $model) {
					return $model->solved_date ? Yii::$app->formatter->asDatetime(strtotime($model->solved_date)) : '-';
				},
				'filter' => DatePicker::widget([
					'model' => $searchModel,
					'attribute' => 'solved_date',
					'clientOptions' => [
						'autoclose' => true,
						'format' => 'yyyy-mm-dd'
					],
					'options' => [
						'autocomplete' => 'off'
					]
				]),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => static function ($url, CasesQSearch $model) {
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
