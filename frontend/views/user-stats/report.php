<?php

use kartik\export\ExportMenu;
use sales\model\user\reports\stats\Metrics;
use sales\model\user\reports\stats\UserStatsReport;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;

/* @var yii\web\View $this */
/* @var UserStatsReport $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'User Stats Report';
$this->params['breadcrumbs'][] = $this->title;

$columns = [];

if ($searchModel->isValid) {
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
        ];
    }
    if (Metrics::isTips($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'tips',
            'format' => 'raw',
        ];
    }
    if (Metrics::isLeadsProcessed($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'leads_processed',
            'format' => 'raw',
        ];
    }
    if (Metrics::isLeadsTrashed($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'leads_trashed',
            'format' => 'raw',
        ];
    }
    if (Metrics::isLeadsToFollowUp($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'leads_follow_up',
            'format' => 'raw',
        ];
    }
    if (Metrics::isLeadsCloned($searchModel->metrics)) {
        $columns[] = [
            'attribute' => 'leads_cloned',
            'format' => 'raw',
        ];
    }
}

?>
<div class="user-stats-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['id' => 'pjax-user-report', 'timeout' => 7000, 'enablePushState' => true, 'scrollTo' => 0]); ?>

    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Search</h2>
            <ul class="nav navbar-right panel_toolbox">
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
        <?php echo ExportMenu::widget([
            'dataProvider' => $dataProvider,
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
        ]); ?>
    </div>

    <br>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null,
        'columns' => $columns,
    ]) ?>

    <?php Pjax::end(); ?>
</div>

<?php
$js = <<<JS
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
