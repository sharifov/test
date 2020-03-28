<?php

use common\models\Department;
use common\models\Project;
use sales\entities\cases\CaseCategory;
use sales\entities\cases\CasesQSearch;
use sales\yii\grid\cases\NeedActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use sales\entities\cases\Cases;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\entities\cases\CasesQSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inbox Queue';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1>
    <i class="fa fa-briefcase"></i> <?= Html::encode($this->title) ?>
</h1>

<div class="cases-q-inbox">

    <?php Pjax::begin(['id' => 'cases-q-inbox-pjax-list', 'timeout' => 5000, 'enablePushState' => true]); ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function (CasesQSearch $model) {
            if ($model->activeFlight) {
                return ['class' => 'danger'];
            }
        },
        'columns' => [
            [
                'attribute' => 'cs_id',
                'label' => 'ID',
            ],
			[
				'attribute' => 'cs_project_id',
				'value' => static function (CasesQSearch $model) {
					return $model->project ? $model->project->name : '';
				},
				'filter' => Project::getList()
			],
            [
                'attribute' => 'cs_subject',
                'contentOptions' => [
                    'style' => 'word-break: break-all; white-space:normal'
                ]
            ],
			[
				'attribute' => 'cs_category_id',
				'value' => static function (CasesQSearch $model) {
					return $model->category ? $model->category->cc_name : '';
				},
				'filter' => CaseCategory::getList()
			],
            [
                'attribute' => 'cs_lead_id',
                'value' => static function (CasesQSearch $model) {
                    return $model->lead ? $model->lead->uid : '';
                },
            ],
            'cs_order_uid',
            [
                'attribute' => 'lastActiveDateFlight',
                'value' => static function (CasesQSearch $model) {

                    if ($model->lastActiveDateFlight) {
                        $out = $model->last_out_date == $model->lastActiveDateFlight ? 'Out <i class="fab fa-hotjar"></i> '
                            : 'Out <i class="fa fa-calendar"></i> ';
                        $out .= Yii::$app->formatter->asDatetime(strtotime($model->last_out_date)) . '<br />';

                        $out .= $model->last_in_date == $model->lastActiveDateFlight ? 'Out <i class="fab fa-hotjar"></i> '
                            : 'In <i class="fa fa-calendar"></i> ';
                        $out .= Yii::$app->formatter->asDatetime(strtotime($model->last_in_date));
                        return $out;
                    }

                    return '<span class="not-set">(not set)</span>';
                },
                'format' => 'raw',
                'options' => ['style' => 'width: 180px']
            ],
			[
				'attribute' => 'cs_dep_id',
				'value' => static function (CasesQSearch $model) {
					return $model->department ? $model->department->dep_name : '';
				},
				'filter' => Department::getList()
			],
            [
                'class' => NeedActionColumn::class,
                'attribute' => 'cs_need_action',
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

    <?php Pjax::end() ?>

</div>
