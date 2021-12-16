<?php

use common\components\grid\DateTimeColumn;
use common\models\Department;
use common\models\Employee;
use common\models\Project;
use sales\access\ListsAccess;
use sales\auth\Auth;
use sales\entities\cases\CaseCategory;
use sales\entities\cases\CasesQSearch;
use common\components\grid\cases\NeedActionColumn;
use common\components\grid\DeadlineColumn;
use sales\helpers\communication\StatisticsHelper;
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\grid\GridView;
use sales\entities\cases\Cases;
use yii\widgets\Pjax;
use common\models\Language;

/**
 * @var $this yii\web\View
 * @var $searchModel sales\entities\cases\CasesQSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $isAgent bool
 */

$this->title = 'Follow Up Queue';
$this->params['breadcrumbs'][] = $this->title;

/** @var Employee $user */
$user = Yii::$app->user->identity;

$lists = new ListsAccess($user->id);
?>
<style>
    .dropdown-menu {
        z-index: 1010 !important;
    }
</style>
<h1>
    <i class="fa fa-recycle"></i> <?= Html::encode($this->title)?>
</h1>

<div class="cases-q-follow-up">

    <?php Pjax::begin(['id' => 'cases-q-follow-up-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'scrollTo' => 0]); ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'id' => 'follow-up-gv',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cs_id',
            [
                'class' => DeadlineColumn::class,
                'timeAttribute' => 'cs_deadline_dt',
                'label' => 'Time left',
                'attribute' => 'time_left',
            ],

            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'cs_project_id',
                'relation' => 'project'
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
                'attribute' => 'nextFlight',
                'value' => static function (CasesQSearch $model) {
                    if ($model->nextFlight) {
                        $out =  '<i class="fa fa-calendar"></i> ';
                        $out .= Yii::$app->formatter->asDate(strtotime($model->nextFlight));
                        return $out;
                    }
                    return '<span class="not-set">(not set)</span>';
                },
                'format' => 'raw',
                'options' => ['style' => 'width: 180px'],
                'filter' => false,
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
                'label' => 'Communication',
                'value' => static function (CasesQSearch $model) {
                    $statistics = new StatisticsHelper($model->cs_id, StatisticsHelper::TYPE_CASE);
                    return Yii::$app->getView()->render(
                        '/partial/_communication_statistic_list',
                        [
                            'statistics' => $statistics->setCountAll(),
                            'lastCommunication' => $statistics::getLastCommunicationByCaseId($model->cs_id),
                        ]
                    );
                },
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
            [
                'label' => 'Pending Time',
                'value' => static function (CasesQSearch $model) {
                    $createdTS = strtotime($model->cs_created_dt);

                    $diffTime = time() - $createdTS;
                    $diffHours = (int) ($diffTime / (60 * 60));

                    return ($diffHours > 3 && $diffHours < 73) ? $diffHours . ' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
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
                'value' => function (CasesQSearch $model) {
                    return $model->getClientTime();
                },
            ],
            [
                'attribute' => 'client_locale',
                'value' => 'client.cl_locale',
                'filter' => Language::getLocaleList(false)
            ],
            /*[
                'attribute' => 'cs_user_id',
                'label' => 'Agent',
                'value' => static function (CasesQSearch $model) {
                    return $model->owner ? $model->owner->username : '';
                },
                'filter' => $lists->getEmployees(),
                'visible' => $user->isSupSuper() || $user->isExSuper() || $user->isAdmin()
            ],*/

            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'label' => 'Agent',
                'attribute' => 'cs_user_id',
                'relation' => 'owner',
                'placeholder' => 'Select User',
                'visible' => $user->isSupSuper() || $user->isExSuper() || $user->isAdmin()
            ],

            [
                'attribute' => 'cs_last_action_dt',
                'label' => 'Last Action',
                'value' => static function (CasesQSearch $model) {
                    $createdTS = strtotime($model->cs_last_action_dt);

                    $diffTime = time() - $createdTS;
                    $diffHours = (int) ($diffTime / (60 * 60));

                    return ($diffHours > 3 && $diffHours < 73) ? $diffHours . ' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {take}',
                'visibleButtons' => [
                    'view' => static function (CasesQSearch $model, $key, $index) {
                        return Auth::can('cases/view', ['case' => $model]);
                    },
                    'take' => static function (CasesQSearch $model, $key, $index) {
                        return Auth::can('cases/take', ['case' => $model]);
                    },
                ],
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
                    'take' => function ($url, Cases $model) {
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
