<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use sales\access\ListsAccess;

$this->title = 'Calls Report';
$this->params['breadcrumbs'][] = $this->title;

/**
 * @var $this yii\web\View
 * @var $searchModel common\models\search\CallSearch;
 * @var $dataProvider yii\data\ActiveDataProvider
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
        //['class' => 'yii\grid\SerialColumn'],
        [
            'label' => 'Username',
            'attribute' => 'c_created_user_id',
            'value' => static function ($searchModel) {
                $employee = \common\models\Employee::findone($searchModel['c_created_user_id']);
                return $employee->username;
            },
            'format' => 'raw',
            'filter' => $list->getEmployees()
        ],
        [   'label' =>'Report Date',
            'attribute' => 'createdDate',
        ],

        [
            'label' => 'Duration',
            'format' => 'raw',
            'value' => function($data) {
                $totalDuration =  $data['outgoingCallsDuration'] + $data['incomingCallsDuration'] + $data['redialCallsDuration'];
                return '<i class="fa fa-clock-o"></i> <span title="'.Yii::$app->formatter->asDuration($totalDuration).'">' . gmdate('H:i:s', $totalDuration) . '</span>';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;']
        ],

        [
            'label' => 'Completed',
            'value' => function($data) {
                return $data['outgoingCallsCompleted'] + $data['incomingCompletedCalls'] + $data['redialCompleted'];
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],

        [
            'label' =>'Duration',
            'attribute' => 'outgoingCallsDuration',
            'headerOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'contentOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'filterOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'format' => 'raw',
            'value' => function($data) {
                return '<i class="fa fa-clock-o"></i> <span title="'.Yii::$app->formatter->asDuration($data['outgoingCallsDuration']).'">' . gmdate('H:i:s', $data['outgoingCallsDuration']) . '</span>';
            }
        ],
        [
            'label' =>'Total',
            'attribute' => 'outgoingCalls',
            'value' => function($data) {
                return $data['outgoingCalls'] ?: '-';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],
        [
            'label' =>'Completed',
            'attribute' => 'outgoingCallsCompleted',
            'value' => function($data) {
                return $data['outgoingCallsCompleted'] ?: '-';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],
        [
            'label' =>'NoAnswer',
            'attribute' => 'outgoingCallsNoAnswer',
            'value' => function($data) {
                return $data['outgoingCallsNoAnswer'] ?: '-';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],
        [
            'label' =>'Busy',
            'attribute' => 'outgoingCallsBusy',
            'value' => function($data) {
                return $data['outgoingCallsBusy'] ?: '-';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],
        [
            'label' =>'Duration',
            'attribute' => 'incomingCallsDuration',
            'headerOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'contentOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'filterOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'format' => 'raw',
            'value' => function($data) {
                return '<i class="fa fa-clock-o"></i> <span title="'.Yii::$app->formatter->asDuration($data['incomingCallsDuration']).'">' . gmdate('H:i:s', $data['incomingCallsDuration']) . '</span>';
            }
        ],
        [
            'label' =>'Completed',
            'attribute' => 'incomingCompletedCalls',
            'value' => function($data) {
                return $data['incomingCompletedCalls'] ?: '-';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],
        [
            'label' =>'Direct Line',
            'attribute' => 'incomingDirectLine',
            'value' => function($data) {
                return $data['incomingDirectLine'] ?: '-';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],
        [
            'label' =>'General Line',
            'attribute' => 'incomingGeneralLine',
            'value' => function($data) {
                return $data['incomingGeneralLine'] ?: '-';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3'],
            'contentOptions' => [
                'style' => 'background-color:#fcf8e3',
                'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3']
        ],

        [
            'label' =>'Duration',
            'attribute' => 'redialCallsDuration',
            'headerOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'contentOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'filterOptions' => ['style' => 'background-color:#fcf8e3; border-left: 2px solid #f0ad4e;'],
            'format' => 'raw',
            'value' => function($data) {
                return '<i class="fa fa-clock-o"></i> <span title="'.Yii::$app->formatter->asDuration($data['redialCallsDuration']).'">' . gmdate('H:i:s', $data['redialCallsDuration']) . '</span>';
            }
        ],

        [
            'label' =>'Total Attempts',
            'attribute' => 'totalAttempts',
            'value' => function($data) {
                return $data['totalAttempts'] ?: '-';
            },
            'headerOptions' => ['style' => 'background-color:#fcf8e3;'],
            'contentOptions' => [
                    'style' => 'background-color:#fcf8e3',
                    'class' => 'text-center'
            ],
            'filterOptions' => ['style' => 'background-color:#fcf8e3;'],
        ],

        [
            'label' =>'Completed',
            'attribute' => 'redialCompleted',
            'value' => function($data) {
                return $data['redialCompleted'] ?: '-';
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
        'fontAwesome' => true,
        //'stream' => false, // this will automatically save file to a folder on web server
        //'deleteAfterSave' => false, // this will delete the saved web file after it is streamed to browser,
        'batchSize' => 10,
        'target' => \kartik\export\ExportMenu::TARGET_BLANK,
        //'linkPath' => '/assets/',
        //'folder' => '@webroot/assets', // this is default save folder on server
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
        'toolbar' => [
            'content' =>'<div class="btn-group">'. Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['report/calls-report'], ['class' => 'btn btn-outline-secondary', 'title'=>'Reset Grid']) .'</div>',
            '{export}',
            $fullExportMenu,
        ],
        'beforeHeader' => [
            [
                'columns' => [
                    ['content' => '', 'options' => ['colspan' => 2]],
                    ['content' => 'Total', 'options' => ['colspan' => 2, 'class' => 'text-success text-center warning', 'style' => 'border-left: 2px solid #f0ad4e;']],
                    ['content' => 'Outgoing Calls', 'options' => ['colspan' => 5, 'class' => 'text-success text-center warning', 'style' => 'border-left: 2px solid #f0ad4e;']],
                    ['content' => 'Incoming Calls', 'options' => ['colspan' => 4, 'class' => 'text-success text-center warning', 'style' => 'border-left: 2px solid #f0ad4e;']],
                    ['content' => 'Redial Calls', 'options' => ['colspan' => 3, 'class' => 'text-success text-center warning', 'style' => 'border-left: 2px solid #f0ad4e;']],
                ],
            ]
        ],
    ]);
    ?>
</div>


