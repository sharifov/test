<?php

use common\components\grid\DateColumn;
use common\components\grid\DateTimeColumn;
use sales\model\user\entity\sales\SalesSearch;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;

/* @var yii\web\View $this */
/* @var SalesSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */
/* @var int $totalCount */
/* @var float $sumGrossProfit */
/* @var int $qualifiedLeadsTaken */
/* @var int $cacheDuration */

$this->title = 'Sales';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-stats-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['id' => 'pjax-sales', 'timeout' => 90000, 'enablePushState' => true]); ?>

    <?php if ($cacheDuration > 0) : ?>
        <div class="alert alert-secondary alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <i class="fa fa-info-circle"></i> Report data is updated every <?php echo gmdate("i", $cacheDuration) ?> minutes
        </div>
    <?php endif ?>

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
            echo $this->render('_search', [
                'model' => $searchModel,
            ]);
            ?>
        </div>
    </div>

    <div class="row" >
        <div class="tile_count" style="width: 100%;">
            <div class="col-md-2 tile_stats_count dev-tile-adjust sales-stats-box">
                <span class="count_top"><i class="fa fa-money"></i> Gross Profit</span>
                <div class="count"><?php echo round($sumGrossProfit, 2) ?></div>
            </div>
            <div class="col-md-2 tile_stats_count dev-tile-adjust sales-stats-box">
                <span class="count_top"><i class="fa fa-line-chart"></i> Leads Sold</span>
                <div class="count"><?php echo $totalCount ?></div>
            </div>
            <div class="col-md-2 tile_stats_count dev-tile-adjust sales-stats-box">
                <span class="count_top"><i class="fa fa-tasks"></i> Qualified Leads</span>
                <div class="count"><?php echo $qualifiedLeadsTaken ?></div>
            </div>
            <div class="col-md-2 tile_stats_count dev-tile-adjust sales-stats-box">
                <span class="count_top"><i class="fa fa-pie-chart"></i> Conversion</span>
                <div class="count"><?php echo $totalCount ?>/<?php echo $qualifiedLeadsTaken ?></div>
                <?php if ($qualifiedLeadsTaken > 0) : ?>
                    <span class="count_bottom">
                        <i class="green">
                            <?php echo round(($totalCount * 100) / $qualifiedLeadsTaken, 2) ?>%
                        </i>
                    </span>
                <?php endif ?>
            </div>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'id',
                'format' => 'raw',
                'label' => 'Lead',
                'value' => static function ($data) {
                    return Html::tag('i', '', ['class' => 'fa fa-arrow-right'])
                        . ' '
                        . Html::a(
                            'lead: ' . $data['id'],
                            ['/lead/view', 'gid' => $data['gid']],
                            ['target' => '_blank', 'data-pjax' => 0]
                        );
                }
            ],
            [
                'attribute' => 'final_profit',
                'format' => 'raw',
                'label' => 'Gross Profit',
                'value' => static function ($data) {
                    return Html::tag('i', '', ['class' => 'fa fa-money']) . ' ' . $data['gross_profit'];
                },
                'contentOptions' => [
                    'style' => 'width:420px'
                ],
            ],
            [
                'attribute' => 'l_status_dt',
                'class' => DateColumn::class,
                'label' => 'Sold Date',
            ],
            [
                'attribute' => 'created',
                'class' => DateColumn::class,
            ],
        ],
    ]) ?>

    <?php Pjax::end(); ?>
</div>

<?php
$css = <<<CSS
    .sales-stats-box {
        margin-bottom: 0!important;
        padding-bottom: 0!important;
    }
CSS;
$this->registerCss($css);
