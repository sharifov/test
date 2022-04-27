<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use src\access\ListsAccess;
use kartik\export\ExportMenu;
use src\helpers\call\CallHelper;

$this->title = 'Calls Report';
$this->params['breadcrumbs'][] = $this->title;

/**
 * @var $this yii\web\View
 * @var $searchModel \src\model\callLog\entity\callLog\search\CallLogSearch;
 * @var $dataProvider yii\data\ArrayDataProvider
 */
$list = new ListsAccess(Yii::$app->user->id);

?>
<div class="calls-report-index">
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
                <?= $this->render('_search_calls', ['model' => $searchModel, 'list' => $list]);  ?>
            </div>
        </div>
    </div>

    <?php
    $gridColumns = [
        [
            'label' => 'Username',
            'attribute' => 'cl_user_id',
            'value' => static function ($data) {
                $employee = \common\models\Employee::findone($data['cl_user_id']);
                return $employee->username;
            },
            'format' => 'raw',
            'filter' => $list->getEmployees()
        ],

        [   'label' => 'Report Date',
            'attribute' => 'createdDate',
        ],

        [
            'label' => 'Duration',
            'format' => 'raw',
            'value' => function ($data) {
                $totalDuration =  $data['inCallsDuration'] + $data['outCallsDuration'] + $data['redialCallsDuration'];
                return '<i class="fa fa-clock-o"></i> <span title="' . Yii::$app->formatter->asDuration($totalDuration) . '">' . CallHelper::customizedDuration($totalDuration ?? 0) . '</span>';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;']
        ],

        [
            'label' => 'Talk Time',
            'format' => 'raw',
            'value' => function ($data) {
                $totalTalkTime =  $data['outCallsTalkTime'] + $data['inCallsDuration'] + $data['redialCallsTalkTime'];
                return '<i class="fa fa-clock-o"></i> <span title="' . Yii::$app->formatter->asDuration($totalTalkTime) . '">' . CallHelper::customizedDuration($totalTalkTime ?? 0) . '</span>';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3;'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3;',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3;']
        ],

        [
            'label' => 'Completed',
            'value' => function ($data) {
                $totalCompleted =  $data['outCallsCompleted'] + $data['inCallsCompleted'] + $data['redialCallsCompleted'];
                return $totalCompleted;
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],

        [
            'label' => 'Complete Talk Time',
            'format' => 'raw',
            'value' => function ($data) {
                $totalTalkTime =  $data['outCallsCompletedDuration'] + $data['inCallsDuration'] + $data['redialCallsCompleteTalkTime'];
                return '<i class="fa fa-clock-o"></i> <span title="' . Yii::$app->formatter->asDuration($totalTalkTime) . '">' . CallHelper::customizedDuration($totalTalkTime ?? 0) . '</span>';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3;'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3;',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3;']
        ],

        [
            'label' => 'Duration',
            'attribute' => 'outCallsDuration',
            'headerOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'contentOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'filterOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'format' => 'raw',
            'value' => function ($data) {
                return '<i class="fa fa-clock-o"></i> <span title="' . Yii::$app->formatter->asDuration($data['outCallsDuration']) . '">' . CallHelper::customizedDuration($data['outCallsDuration'] ?? 0) . '</span>';
            }
        ],
        [
            'label' => 'Talk Time',
            'attribute' => 'outCallsTalkTime',
            'format' => 'raw',
            'value' => function ($data) {
                return '<i class="fa fa-clock-o"></i> <span title="' . Yii::$app->formatter->asDuration($data['outCallsTalkTime']) . '">' . CallHelper::customizedDuration($data['outCallsTalkTime'] ?? 0) . '</span>';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],
        [
            'label' => 'Total',
            'attribute' => 'totalOutCalls',
            'value' => function ($data) {
                return $data['totalOutCalls'] ?: '-';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],
        [
            'label' => 'Completed Duration',
            'attribute' => 'outCallsCompletedDuration',
            'format' => 'raw',
            'value' => function ($data) {
                return '<i class="fa fa-clock-o"></i> <span title="' . Yii::$app->formatter->asDuration($data['outCallsCompletedDuration']) . '">' . CallHelper::customizedDuration($data['outCallsCompletedDuration'] ?? 0) . '</span>';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],
        [
            'label' => 'Completed',
            'attribute' => 'outCallsCompleted',
            'value' => function ($data) {
                return $data['outCallsCompleted'] ?: '-';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],
        [
            'label' => 'No Answer',
            //'attribute' => 'outCallsNoAnswer',
            'value' => function ($data) {
                $noAnswer = $data['totalOutCalls'] - $data['outCallsCompleted'];
                return $noAnswer ?: '-';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],
        [
            'label' => 'Talk time',
            'attribute' => 'inCallsDuration',
            'headerOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'contentOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'filterOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'format' => 'raw',
            'value' => function ($data) {
                return '<i class="fa fa-clock-o"></i> <span title="' . Yii::$app->formatter->asDuration($data['inCallsDuration']) . '">' . CallHelper::customizedDuration($data['inCallsDuration'] ?? 0) . '</span>';
            }
        ],
        [
            'label' => 'Completed',
            'attribute' => 'inCallsCompleted',
            'value' => function ($data) {
                return $data['inCallsCompleted'] ?: '-';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],
        [
            'label' => 'Direct Line',
            'attribute' => 'inCallsDirectLine',
            'value' => function ($data) {
                return $data['inCallsDirectLine'] ?: '-';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],
        [
            'label' => 'General Line',
            'attribute' => 'inCallsGeneralLine',
            'value' => function ($data) {
                return $data['inCallsGeneralLine'] ?: '-';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],

        [
            'label' => 'Duration',
            'attribute' => 'redialCallsDuration',
            'headerOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'contentOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'filterOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'format' => 'raw',
            'value' => function ($data) {
                return '<i class="fa fa-clock-o"></i> <span title="' . Yii::$app->formatter->asDuration($data['redialCallsDuration']) . '">' . CallHelper::customizedDuration($data['redialCallsDuration'] ?? 0)  . '</span>';
            }
        ],

        [
            'label' => 'Talk time',
            'attribute' => 'redialCallsTalkTime',
            'format' => 'raw',
            'value' => function ($data) {
                return '<i class="fa fa-clock-o"></i> <span title="' . Yii::$app->formatter->asDuration($data['redialCallsTalkTime']) . '">' . CallHelper::customizedDuration($data['redialCallsTalkTime'] ?? 0) . '</span>';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3;'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3;'],
        ],

        [
            'label' => 'Total Attempts',
            'attribute' => 'redialCallsTotalAttempts',
            'value' => function ($data) {
                return $data['redialCallsTotalAttempts'] ?: '-';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3;'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3;'],
        ],

        [
            'label' => 'Completed',
            'attribute' => 'redialCallsCompleted',
            'value' => function ($data) {
                return $data['redialCallsCompleted'] ?: '-';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],

        [
            'label' => 'Complete Talk time',
            'attribute' => 'redialCallsCompleteTalkTime',
            'format' => 'raw',
            'value' => function ($data) {
                return '<i class="fa fa-clock-o"></i> <span title="' . Yii::$app->formatter->asDuration($data['redialCallsCompleteTalkTime']) . '">' . CallHelper::customizedDuration($data['redialCallsCompleteTalkTime'] ?? 0) . '</span>';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],
    ];

    $fullExportMenu = \kartik\export\ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'exportConfig' => [
            ExportMenu::FORMAT_PDF => [
                'pdfConfig' => [
                    'mode' => 'c',
                    'format' => 'A4-L',
                ]
            ]
        ],
        'fontAwesome' => true,
        'bsVersion' => '3.x',
        'timeout' => 60,
        'dropdownOptions' => [
            'label' => 'Full Export'
        ],
        'columnSelectorOptions' => [
            'label' => 'Export Fields'
        ]
    ]);

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
        'responsive' => true,
        'hover' => true,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Calls Data</h3>',
        ],
        'export' => [
            'label' => 'Page'
        ],
        'exportConfig' => [
            'html' => [],
            'csv' => [],
            'txt' => [],
            'xls' => [],
            'pdf' => [
                'config' => [
                    'mode' => 'c',
                ]
            ],
            'json' => [],
        ],
        'toolbar' => [
            'content' => '<div class="btn-group">' . Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['report/calls-report'], ['class' => 'btn btn-outline-secondary', 'title' => 'Reset Grid']) . '</div>',
            '{export}',
            $fullExportMenu,
        ],
        'beforeHeader' => [
            [
                'columns' => [
                    ['content' => '', 'options' => ['colspan' => 2]],
                    ['content' => 'Total', 'options' => ['colspan' => 4, 'class' => 'text-success text-center warning', 'style' => 'border-left: 2px solid #f0ad4e;']],
                    ['content' => 'Outgoing Calls', 'options' => ['colspan' => 6, 'class' => 'text-success text-center warning', 'style' => 'border-left: 2px solid #f0ad4e;']],
                    ['content' => 'Complete Incoming Calls', 'options' => ['colspan' => 4, 'class' => 'text-success text-center warning', 'style' => 'border-left: 2px solid #f0ad4e;']],
                    ['content' => 'Redial Calls', 'options' => ['colspan' => 4, 'class' => 'text-success text-center warning', 'style' => 'border-left: 2px solid #f0ad4e;']],
                ],
            ]
        ],
    ]);
    ?>
</div>


