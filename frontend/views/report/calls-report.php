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
            'value' => function ($searchModel) {
                $employee = \common\models\Employee::findone($searchModel['c_created_user_id']);
                return $employee->username;
            },
            'format' => 'raw',
            'filter' => $list->getEmployees()
        ],
        [
            'attribute' => 'createdDate',
        ],
        [
            'attribute' => 'outgoingCallsDuration',
        ],
        [
            'attribute' => 'outgoingCalls',
        ],
        [
            'attribute' => 'outgoingCallsCompleted',
        ],
        [
            'attribute' => 'outgoingCallsNoAnswer',
        ],
        [
            'attribute' => 'outgoingCallsCanceled',
        ],
        [
            'attribute' => 'incomingCallsDuration',
        ],
        [
            'attribute' => 'incomingCalls',
        ],
        [
            'attribute' => 'incomingDirectLine',
        ],
        [
            'attribute' => 'incomingGeneralLine',
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
            'content' => Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['report/calls-report'], ['class' => 'btn btn-default', 'title'=>'Reset Grid']),
            '{export}',
            $fullExportMenu,
        ]
    ]);
    ?>
</div>


