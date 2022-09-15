<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use common\models\Department;
use common\models\Project;
use src\auth\Auth;
use src\entities\cases\CaseCategory;
use src\entities\cases\CasesQSearch;
use common\components\grid\cases\NeedActionColumn;
use src\helpers\communication\StatisticsHelper;
use src\model\saleTicket\entity\SaleTicket;
use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use src\entities\cases\Cases;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;
use common\models\Language;

/**
 * @var $this yii\web\View
 * @var $searchModel src\entities\cases\CasesQSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $isAgent bool
 */

$this->title = 'Cross Sale Queue';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1>
    <i class="fa fa-briefcase"></i> <?= Html::encode($this->title) ?>
</h1>

<div class="cases-q-cross-sale-inbox">

    <?php Pjax::begin(['id' => 'cases-q-inbox-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'scrollTo' => 0]); ?>

    <?= GridView::widget([
        'id' => 'inbox-gv',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'cs_id',
                'label' => 'ID',
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
                'value' => static function (CasesQSearch $model) {
                    return $model->getClientTime();
                },
            ],
            [
                'attribute' => 'client_locale',
                'value' => 'client.cl_locale',
                'filter' => Language::getLocaleList(false)
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
                        ]);
                    }
                ],
            ]

        ],
    ]); ?>

    <?php Pjax::end() ?>

</div>
