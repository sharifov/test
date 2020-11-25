<?php

use common\models\Department;
use common\models\Employee;
use common\models\Project;
use sales\access\ListsAccess;
use sales\auth\Auth;
use sales\entities\cases\CaseCategory;
use sales\entities\cases\CasesQSearch;
use common\components\grid\cases\NeedActionColumn;
use sales\helpers\communication\StatisticsHelper;
use sales\model\saleTicket\entity\SaleTicket;
use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use sales\entities\cases\Cases;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\entities\cases\CasesQSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

/** @var Employee $user */
$user = Yii::$app->user->identity;

$this->title = 'Processing Queue';
$this->params['breadcrumbs'][] = $this->title;
$lists = new ListsAccess($user->id);
?>
<style>
    .dropdown-menu {
        z-index: 1010 !important;
    }
</style>
<h1>
    <i class="fa fa-spinner"></i> <?= Html::encode($this->title) ?>
</h1>

<div class="cases-q-processing">

    <?php Pjax::begin(['id' => 'cases-q-processing-pjax-list', 'timeout' => 5000, 'enablePushState' => true]); ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cs_id',

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

                    return ($diffHours > 3 && $diffHours < 73 ) ? $diffHours . ' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
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
                'value' => static function (CasesQSearch $model) {
                    return $model->getClientTime();
                },
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
//                'visible' => $user->isSupSuper() || $user->isExSuper() || $user->isAdmin()
            ],

            [
                'attribute' => 'cs_last_action_dt',
                'label' => 'Last Action',
                'value' => static function (CasesQSearch $model) {
                    $createdTS = strtotime($model->cs_last_action_dt);

                    $diffTime = time() - $createdTS;
                    $diffHours = (int) ($diffTime / (60 * 60));

                    return ($diffHours > 3 && $diffHours < 73 ) ? $diffHours . ' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                },
            ],
            [
                'attribute' => 'css_penalty_type',
                'value' => static function (CasesQSearch $model) {
                    return $model->css_penalty_type ? SaleTicket::getPenaltyTypeName($model->css_penalty_type) : '-';
                },
                'filter' => SaleTicket::getAirlinePenaltyList()
            ],
            [
                'attribute' => 'css_departure_dt',
                'value' => static function (CasesQSearch $model) {
                    return $model->css_departure_dt ? Yii::$app->formatter->asDatetime(strtotime($model->css_departure_dt)) : '-';
                },
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'css_departure_dt',
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
                'template' => '{view} {take-over}',
                'visibleButtons' => [
                    'view' => static function (CasesQSearch $model, $key, $index) {
                        return Auth::can('cases/view', ['case' => $model]);
                    },
                    'take-over' => function (CasesQSearch $model, $key, $index) {
                        return Auth::can('cases/takeOver', ['case' => $model]);
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
                    'take-over' => function ($url, Cases $model) {
                        return Html::a('<i class="fa fa-download"></i> Take Over', ['cases/take-over', 'gid' => $model->cs_gid], [
                            'class' => 'btn btn-primary btn-xs take-processing-btn',
                            'data-pjax' => 0,
                            /*'data' => [
                                'confirm' => 'Are you sure you want to take over this Case?',
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
