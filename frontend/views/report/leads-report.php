<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use sales\access\ListsAccess;

$this->title = 'Leads Report';
$this->params['breadcrumbs'][] = $this->title;

/**
 * @var $this yii\web\View
 * @var $searchModel common\models\search\LeadSearch;
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
                <?= $this->render('_search_leads_report' , ['model' => $searchModel, 'list' => $list]);  ?>
            </div>
        </div>
    </div>

    <?php

    $gridColumns = [
        [
            'label' => 'Username',
            'attribute' => 'user_id',
            'value' => function ($searchModel) {
                $employee = \common\models\Employee::findone($searchModel['user_id']);
                return $employee->username;
            },
            'format' => 'raw',
            'filter' => $list->getEmployees()
        ],
        [
            'label' => 'Report Date',
            'attribute' => 'created_date',
        ],
        [
            'attribute' => 'newTotal',
        ],
        [
            'attribute' => 'inboxLeadsTaken',
        ],
        [
            'attribute' => 'callLeadsTaken',
        ],
        [
            'attribute' => 'redialLeadsTaken',
        ],
        [
            'attribute' => 'leadsCreated',
        ],
        [
            'attribute' => 'leadsCloned',
        ],
        [
            'attribute' => 'followUpTotal',
        ],
        [
            'attribute' => 'followUpLeadsTaken',
        ],
        [
            'label' => 'Follow Up Leads Assigned By',
            'value' => function ($searchModel) {
                return $searchModel['followUpTotal'] - $searchModel['followUpLeadsTaken'];
            },
        ],
        [
            'attribute' => 'trashLeads',
        ],
        [
            'attribute' => 'soldLeads',
        ],
        [
            'attribute' => 'profit',
        ],
        [
            'attribute' => 'tips',
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
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Leads Data</h3>',
        ],
        'export' => [
            'label' => 'Page'
        ],
        'toolbar' => [
            'content' => Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['report/leads-report'], ['class' => 'btn btn-default', 'title'=>'Reset Grid']),
            '{export}',
            $fullExportMenu,
        ],
        'beforeHeader' => [
            [
                /*'columns' => [
                    ['content' => '', 'options' => ['colspan' => 2]],
                    ['content' => 'Outgoing Calls', 'options' => ['colspan' => 5, 'class' => 'text-success text-center warning', 'style' => 'border-left: 2px solid #f0ad4e;']],
                    ['content' => 'Incoming Calls', 'options' => ['colspan' => 5, 'class' => 'text-success text-center warning', 'style' => 'border-left: 2px solid #f0ad4e;']],
                ],*/
            ]
        ],
    ]);

    ?>




</div>