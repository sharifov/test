<?php

use kartik\export\ExportMenu;
use kartik\grid\GridView;
use modules\userStats\src\abac\UserStatsAbacObject;
use src\model\user\reports\stats\Metrics;
use src\model\user\reports\stats\UserStatsReport;
use src\model\userModelSetting\service\UserModelSettingHelper;
use yii\bootstrap4\Html;
use yii\bootstrap4\Modal;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;

/* @var yii\web\View $this */
/* @var UserStatsReport $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */
/* @var bool $showReport */
/* @var array $summaryStats */

$this->title = 'User Stats Report';
$this->params['breadcrumbs'][] = $this->title;
$totalResultsProvider = new ArrayDataProvider([
    'allModels' => $summaryStats,
]);
$columns = [];
$isUserHasAccessToReportByLeads = Yii::$app->abac->can(null, UserStatsAbacObject::OBJ_USER_STATS, UserStatsAbacObject::ACTION_ACCESS);

if ($showReport) {
    if ($searchModel->isGroupByUserGroup()) {
        $columns[] = [
            'attribute' => 'group_name',
            'label' => 'Group',
            'format' => 'raw',
        ];
    } elseif ($searchModel->isGroupByUserName()) {
        $columns[] = [
            'attribute' => 'username',
            'value' => static function ($model) {
                return Html::a(
                    $model['username'] . ' (' . $model['id'] . ')',
                    ['user/info', 'id' => $model['id']],
                    ['title' => 'User info', 'target' => '_blank']
                );
            },
            'label' => 'User',
            'format' => 'raw',
        ];
    } elseif ($searchModel->isGroupByUserRole()) {
        $columns[] = [
            'attribute' => 'role_name',
            'value' => static function ($model) {
                return $model['role_name'];
            },
            'label' => 'Role',
            'format' => 'raw',
        ];
    }
    if (Metrics::isLeadsCreated($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'leads_created',
            'value' => static fn (array $model): string =>
                !$isUserHasAccessToReportByLeads
                    ? $model['leads_created']
                    : Html::a($model['leads_created'], 'javascript:void(0)', [
                        'class' => 'showModalButton',
                        'title' =>  'Leads created by ' . $model['username'],
                        'data-modal_id' => 'lg',
                        'data-content-url' => Url::to([
                            'user-stats/ajax-show-user-leads',
                            'user' => $model['id'],
                            'type' => 'created'
                        ])
                    ]),
            'format' => 'raw',
        ];
    }
    if (Metrics::isSalesConversion($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'conversion_percent',
            'format' => 'raw',
        ];
    }
    if (Metrics::isSoldLeads($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'sold_leads',
            'value' => static fn (array $model): string =>
                !$isUserHasAccessToReportByLeads
                    ? $model['sold_leads']
                    : Html::a($model['sold_leads'], 'javascript:void(0)', [
                        'class' => 'showModalButton',
                        'title' =>  'Sold Leads by ' . $model['username'],
                        'data-modal_id' => 'lg',
                        'data-content-url' => Url::to([
                            'user-stats/ajax-show-user-leads',
                            'user' => $model['id'],
                            'type' => 'sold'
                        ])
                    ]),
            'format' => 'raw',
        ];
    }
    if (Metrics::isSplitShare($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'split_share',
            'format' => 'raw',
        ];
    }
    if (Metrics::isQualifiedLeadsTaken($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'qualified_leads_taken',
            'format' => 'raw',
        ];
    }
    if (Metrics::isGrossProfit($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'gross_profit',
            'format' => 'raw',
            'value' => function ($model) {
                $value = ArrayHelper::getValue($model, 'gross_profit');
                return Yii::$app->formatter->asNumCurrency($value, 'USD');
            }
        ];
    }
    if (Metrics::isTips($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'tips',
            'format' => 'raw',
            'value' => function ($model) {
                $value = ArrayHelper::getValue($model, 'tips');
                return Yii::$app->formatter->asNumCurrency($value, 'USD');
            }
        ];
    }
    if (Metrics::isLeadsProcessed($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'leads_processed',
            'value' => static fn (array $model): string =>
                !$isUserHasAccessToReportByLeads
                    ? $model['leads_processed']
                    : Html::a($model['leads_processed'], 'javascript:void(0)', [
                        'class' => 'showModalButton',
                        'title' =>  'Leads Processed by ' . $model['username'],
                        'data-modal_id' => 'lg',
                        'data-content-url' => Url::to([
                            'user-stats/ajax-show-user-leads',
                            'user' => $model['id'],
                            'type' => 'processed'
                        ])
                    ]),
            'format' => 'raw',
        ];
    }
    if (Metrics::isLeadsTrashed($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'leads_trashed',
            'value' => static fn (array $model): string =>
                !$isUserHasAccessToReportByLeads
                    ? $model['leads_trashed']
                    : Html::a($model['leads_trashed'], 'javascript:void(0)', [
                        'class' => 'showModalButton',
                        'title' =>  'Leads Trashed by ' . $model['username'],
                        'data-modal_id' => 'lg',
                        'data-content-url' => Url::to([
                            'user-stats/ajax-show-user-leads',
                            'user' => $model['id'],
                            'type' => 'trashed'
                        ])
                    ]),
            'format' => 'raw',
        ];
    }
    if (Metrics::isLeadsToFollowUp($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'leads_follow_up',
            'value' => static fn (array $model): string =>
                !$isUserHasAccessToReportByLeads
                    ? $model['leads_follow_up']
                    : Html::a($model['leads_follow_up'], 'javascript:void(0)', [
                        'class' => 'showModalButton',
                        'title' =>  'Leads Follow Up by ' . $model['username'],
                        'data-modal_id' => 'lg',
                        'data-content-url' => Url::to([
                            'user-stats/ajax-show-user-leads',
                            'user' => $model['id'],
                            'type' => 'follow_up'
                        ])
                    ]),
            'format' => 'raw',
        ];
    }
    if (Metrics::isLeadsCloned($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'leads_cloned',
            'value' => static fn (array $model): string =>
                !$isUserHasAccessToReportByLeads
                    ? $model['leads_cloned']
                    : Html::a($model['leads_cloned'], 'javascript:void(0)', [
                        'class' => 'showModalButton',
                        'title' =>  'Leads Cloned by ' . $model['username'],
                        'data-modal_id' => 'lg',
                        'data-content-url' => Url::to([
                            'user-stats/ajax-show-user-leads',
                            'user' => $model['id'],
                            'type' => 'cloned'
                        ])
                    ]),
            'format' => 'raw',
        ];
    }
    if (Metrics::isCallPriorityCurrent($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'call_priority_current',
            'format' => 'raw',
        ];
    }
    if (Metrics::isSalesConversionCallPriority($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'sales_conversion_call_priority',
            'format' => 'raw',
        ];
    }
    if (Metrics::isGrossProfitCallPriority($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'gross_profit_call_priority',
            'format' => 'raw',
        ];
    }
}
?>

<div class="user-stats-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Search</h2>
            <ul class="nav navbar-right panel_toolbox" style="min-width: 0;">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <?php
            echo $this->render('_search_report', [
                'model' => $searchModel,
            ]);
            ?>
        </div>
    </div>

    <div class="d-flex">
        <?php
        $exportMenu = ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'layout' => "{errors}\n{summary}\n{items}\n{pager}",
            'columns' => $columns,
            'exportConfig' => [
                ExportMenu::FORMAT_PDF => [
                    'pdfConfig' => [
                        'mode' => 'c',
                        'format' => 'A4-L',
                    ]
                ]
            ],
            'target' => \kartik\export\ExportMenu::TARGET_BLANK,
            'bsVersion' => '3.x',
            'fontAwesome' => true,
            'dropdownOptions' => [
                'label' => 'Full Export'
            ],
            'columnSelectorOptions' => [
                'label' => 'Export Fields'
            ],
            'showConfirmAlert' => false,
            'options' => [
                'id' => 'export-links'
            ],
        ]);
        ?>

    </div>

    <br>

    <?php if ($showReport) : ?>
        <div class="row">
            <div class="col-md-12">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => null,
                    'columns' => $columns,
                    'responsive' => true,
                    'hover' => true,
                    'panel' => [
                        'type' => GridView::TYPE_PRIMARY,
                        'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Report</h3>',
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
                        //'content' => '<div class="btn-group">' . \yii\helpers\Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['report/leads-report'], ['class' => 'btn btn-outline-secondary', 'title' => 'Reset Grid']) . '</div>',
                        '{export}',
                        $exportMenu,
                    ],
                ]) ?>

            </div>
        </div>
    <div class="row">
        <div class="col-md-12">
            <?= GridView::widget([
                'dataProvider' => $totalResultsProvider,
                'responsive' => true,
                'hover' => true,
                'panel' => [
                    'type' => GridView::TYPE_PRIMARY,
                    'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Total Report</h3>',
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
                    '{export}',
                    ExportMenu::widget([
                        'dataProvider' => $totalResultsProvider,
                        'columns' => [
                                'Name',
                                'total',
                                'average'
                        ],
                        'exportConfig' => [
                            ExportMenu::FORMAT_PDF => [
                                'pdfConfig' => [
                                    'mode' => 'c',
                                    'format' => 'A4-L',
                                ]
                            ]
                        ],
                        'target' => \kartik\export\ExportMenu::TARGET_BLANK,
                        'bsVersion' => '3.x',
                        'fontAwesome' => true,
                        'dropdownOptions' => [
                            'label' => 'Full Export'
                        ],
                        'columnSelectorOptions' => [
                            'label' => 'Export Fields'
                        ],
                        'showConfirmAlert' => false,
                        'options' => [
                            'id' => 'export-total-links'
                        ],
                    ])
                ],
            ]);
            ?>
        </div>
    </div>

    <?php endif; ?>

</div>

<?php
$js = <<<JS
$(document).on('click', '.showModalButton', function(){
    let id = $(this).data('modal_id');
    let url = $(this).data('content-url');

    $('#modal-' + id + '-label').html($(this).attr('title'));
    $('#modal-' + id).modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');

    $.post(url, function(data) {
        $('#modal-' + id).find('.modal-body').html(data);
    });
});

$(document).on('beforeSubmit', '#UserStatsReportForm', function(event) {
    let btn = $(this).find('.js-user-stats-btn');
    btn.html('<span class="spinner-border spinner-border-sm"></span> Loading');        
    btn.prop("disabled", true)
});
JS;
$this->registerJs($js, View::POS_READY);

$css = <<<CSS
    #w1-filters { 
        display: none;
    }
CSS;
$this->registerCss($css);
?>
