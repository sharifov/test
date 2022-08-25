<?php

use common\components\grid\DateColumn;
use common\components\grid\DateTimeColumn;
use src\model\user\entity\sales\SalesSearch;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;

/* @var yii\web\View $this */
/* @var SalesSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */
/* @var int $totalCount */
/* @var float $sumGrossProfit */
/* @var float $sumShare */
/* @var int $qualifiedLeadsTakenCount */
/* @var int $cacheDuration */
/* @var yii\data\ActiveDataProvider $searchQualifiedLeads */

$this->title = 'My Sales';
$this->params['breadcrumbs'][] = $this->title;

$tabs[] = [
    'id' => 'sold-leads',
    'name' => '<i class="fa fa-line-chart"></i> Sold Leads <sup>(' . $totalCount . ')</sup>',
    'content' => $this->render('partial/_sold_leads', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel]),
];

$tabs[] = [
    'id' => 'qualified-leads',
    'name' => '<i class="fa fa-tasks"></i> Qualified Leads <sup>(' . $qualifiedLeadsTakenCount . ')</sup>',
    'content' => $this->render('partial/_qualified_leads', ['dataProvider' => $searchQualifiedLeads, 'searchModel' => $searchModel]),
];

?>
<div class="user-stats-index">

    <h1><?= Html::encode($this->title) ?></h1>

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
            <div class="tile_stats_count sales-stats-box">
                <span class="count_top"><i class="fa fa-money"></i> Gross Profit</span>
                <div class="count"><?php echo round($sumGrossProfit, 2) ?></div>
            </div>
            <div class="tile_stats_count sales-stats-box">
                <span class="count_top"><i class="fa fa-line-chart"></i> Leads Sold</span>
                <div class="count"><?php echo $totalCount ?></div>
            </div>
            <div class="tile_stats_count sales-stats-box">
                <span class="count_top"><i class="fa fa-share-alt-square"></i> Split Share</span>
                <div class="count"><?php echo $sumShare ?></div>
            </div>
            <div class="tile_stats_count sales-stats-box">
                <span class="count_top"><i class="fa fa-tasks"></i> Qualified Leads</span>
                <div class="count"><?php echo $qualifiedLeadsTakenCount ?></div>
            </div>
            <div class="tile_stats_count sales-stats-box">
                <span class="count_top"><i class="fa fa-pie-chart"></i> Conversion</span>
                <div class="count"><?= ($qualifiedLeadsTakenCount > 0) ? round(($sumShare * 100) / $qualifiedLeadsTakenCount, 2) : 0 ?>%</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <?php foreach ($tabs as $key => $tab) : ?>
                        <?php if ($key === 0) : ?>
                            <a class="nav-item nav-link active" id="nav-<?= $tab['id']?>-tab" data-toggle="tab" href="#nav-<?= $tab['id']?>" role="tab" aria-controls="nav-<?= $tab['id']?>" aria-selected="true"><?= $tab['name']?></a>
                        <?php else : ?>
                            <a class="nav-item nav-link" id="nav-<?= $tab['id']?>-tab" data-toggle="tab" href="#nav-<?= $tab['id']?>" role="tab" aria-controls="nav-<?= $tab['id']?>" aria-selected="false"><?= $tab['name']?></a>
                        <?php endif;?>
                    <?php endforeach; ?>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <br>
                <?php foreach ($tabs as $key => $tab) : ?>
                    <?php if ($key === 0) : ?>
                        <div class="tab-pane fade show active" id="nav-<?= $tab['id']?>" role="tabpanel" aria-labelledby="nav-<?= $tab['id']?>-tab"><?= $tab['content']?></div>
                    <?php else : ?>
                        <div class="tab-pane fade" id="nav-<?= $tab['id']?>" role="tabpanel" aria-labelledby="nav-<?= $tab['id']?>-tab"><?= $tab['content']?></div>
                    <?php endif;?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>

<?php
$css = <<<CSS
    .sales-stats-box {
        width: auto;
        display: inline-block;
      
        margin-bottom: 0!important;
        padding-bottom: 0!important;
    }
CSS;
$this->registerCss($css);
