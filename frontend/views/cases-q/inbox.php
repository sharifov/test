<?php

use common\models\Department;
use common\models\Project;
use sales\entities\cases\CasesCategory;
use sales\entities\cases\CasesQSearch;
use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use sales\entities\cases\Cases;

/* @var $this yii\web\View */
/* @var $searchModel sales\entities\cases\CasesQSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inbox Queue';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1>
    <i class="fa fa-briefcase"></i> <?= Html::encode($this->title) ?>
</h1>

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
                'label' => 'Pending Time',
                'value' => static function (CasesQSearch $model) {
                    $createdTS = strtotime($model->cs_created_dt);
    
                    $diffTime = time() - $createdTS;
                    $diffHours = (int) ($diffTime / (60 * 60));
    
                    return ($diffHours > 3 && $diffHours < 73 ) ? $diffHours.' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                },
                'options' => [
                    'style' => 'width:180px'
                ],
                'format' => 'raw',
                'visible' => ! $isAgent,
            ],
            [
                'header' => 'Client time',
                'format' => 'raw',
                'value' => static function(CasesQSearch $model) {
                    return $model->getClientTime();
                },
            ],
            [
                'header' => 'Agent',
                'format' => 'raw',
                'value' => static function (CasesQSearch $model) {
                    return $model->owner ? '<i class="fa fa-user"></i> ' . $model->owner->username : '-';
                },
            ],
			[
				'attribute' => 'cs_updated_dt',
				'label' => 'Last Action',
				'value' => static function (CasesQSearch $model) {
					$createdTS = strtotime($model->cs_updated_dt);

					$diffTime = time() - $createdTS;
					$diffHours = (int) ($diffTime / (60 * 60));

					return ($diffHours > 3 && $diffHours < 73 ) ? $diffHours.' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
				},
			],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {take}',
                'visibleButtons' => [
                    'take' => static function (CasesQSearch $model, $key, $index) {
                        return !$model->isOwner(Yii::$app->user->id);
                    },
                ],
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
                    'take' => static function ($url, CasesQSearch $model) {
                        return Html::a('<i class="fa fa-download"></i> Take', ['cases/take', 'gid' => $model->cs_gid], [
                            'class' => 'btn btn-primary btn-xs take-processing-btn',
                            'data-pjax' => 0,
                            /*'data' => [
                                'confirm' => 'Are you sure you want to take this Case?',
                                //'method' => 'post',
                            ],*/
                        ]);
                    }
                ],
            ]

        ],
    ]); ?>

</div>
