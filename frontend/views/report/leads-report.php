<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use src\access\ListsAccess;
use kartik\export\ExportMenu;

$this->title = 'Leads Report';
$this->params['breadcrumbs'][] = $this->title;

/**
 * @var $this yii\web\View
 * @var $searchModel common\models\search\LeadSearch;
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
                <?= $this->render('_search_leads_report', ['model' => $searchModel, 'list' => $list]);  ?>
            </div>
        </div>
    </div>

    <?php
    $gridColumns = [
        [
            'label' => 'Username',
            'attribute' => 'lfOwnerId',
            'value' => static function ($data) {
                $employee = \common\models\Employee::findone($data['user_id']);
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
            'label' => 'New Total',
            'attribute' => 'newTotal',
            'value' => static function ($data) {
                return $data['newTotal'] ?: '-';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],
        [
            'label' => 'Inbox Leads Taken',
            'attribute' => 'inboxLeadsTaken',
            'value' => function ($data) {
                return $data['inboxLeadsTaken'] ?: '-';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],
        [
            'label' => 'Call Leads Taken',
            'attribute' => 'callLeadsTaken',
            'value' => function ($data) {
                return $data['callLeadsTaken'] ?: '-';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],
        [
            'label' => 'Redial Leads Taken',
            'attribute' => 'redialLeadsTaken',
            'value' => function ($data) {
                return $data['redialLeadsTaken'] ?: '-';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],
        [
            'label' => 'Leads Created',
            'attribute' => 'leadsCreated',
            'value' => function ($data) {
                return $data['leadsCreated'] ?: '-';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],
        [
            'label' => 'Leads Cloned',
            'attribute' => 'leadsCloned',
            'value' => function ($data) {
                return $data['leadsCloned'] ?: '-';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],
        [
            'label' => 'Follow Up Total',
            'attribute' => 'followUpTotal',
            'value' => function ($data) {
                return $data['followUpTotal'] ?: '-';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],
        [
            'label' => 'To Follow Up',
            'attribute' => 'toFollowUp',
            'value' => function ($data) {
                return $data['toFollowUp'] ?: '-';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],
        [
            'label' => 'Follow Up Leads Taken',
            'attribute' => 'followUpLeadsTaken',
            'value' => function ($data) {
                return $data['followUpLeadsTaken'] ?: '-';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],
        [
            'label' => 'Follow Up Leads Assigned By',
            'value' => static function ($searchModel) {
                return $searchModel['followUpTotal'] - $searchModel['followUpLeadsTaken'] ?: '-';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],
        [
            'label' => 'Trash Leads',
            'attribute' => 'trashLeads',
            'value' => function ($data) {
                return $data['trashLeads'] ?: '-';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],
        [
            'label' => 'Sold Leads',
            'attribute' => 'soldLeads',
            'value' => function ($data) {
                return $data['soldLeads'] ?: '-';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],
        [
            'label' => 'Profit',
            'attribute' => 'profit',
            'value' => function ($data) {
                return number_format($data['profit']) ?: '-';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
        ],
        [
            'label' => 'Tips',
            'attribute' => 'tips',
            'value' => function ($data) {
                return number_format($data['tips']) ?: '-';
            },
            'contentOptions' => [
                'class' => 'text-center'
            ],
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
        'target' => \kartik\export\ExportMenu::TARGET_BLANK,

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
        'exportConfig' => [
            'html' => [],
            'csv' => [],
            'txt' => [],
            'xls' => [],
            'pdf' => [
                'config' => [
                    'mode' => 'c',
                    'format' => 'A4-L',
                ]
            ],
            'json' => [],
        ],
        'toolbar' => [
            'content' => '<div class="btn-group">' . Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['report/leads-report'], ['class' => 'btn btn-outline-secondary', 'title' => 'Reset Grid']) . '</div>',
            '{export}',
            $fullExportMenu,
        ],
    ]);
    ?>
</div>