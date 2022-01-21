<?php

use common\models\Department;
use common\models\Employee;
use common\models\Project;
use src\access\ListsAccess;
use dosamigos\datepicker\DatePicker;
use src\auth\Auth;
use src\entities\cases\CaseCategory;
use src\helpers\communication\StatisticsHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use src\entities\cases\Cases;
use src\entities\cases\CasesQSearch;
use yii\widgets\Pjax;
use common\models\Language;

/**
 * @var $this yii\web\View
 * @var $searchModel src\entities\cases\CasesQSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

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

<div class="cases-q-solved">

    <?php Pjax::begin(['id' => 'cases-q-solved-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'scrollTo' => 0]); ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'id' => 'solved-gv',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cs_id',
            'cs_gid',

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
                    return Yii::$app->getView()->render('/partial/_communication_statistic_list', ['statistics' => $statistics->setCountAll()]);
                },
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
            [
                'attribute' => 'client_locale',
                'value' => 'client.cl_locale',
                'filter' => Language::getLocaleList(false)
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
                'visibleButtons' => [
                    'view' => static function (CasesQSearch $model, $key, $index) {
                        return Auth::can('cases/view', ['case' => $model]);
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
                ],
            ]

        ],
    ]); ?>

    <?php Pjax::end() ?>

</div>
