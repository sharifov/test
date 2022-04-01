<?php

use common\models\Employee;
use kartik\select2\Select2;
use src\model\lead\reports\HeatMapLeadSearch;
use src\model\lead\reports\HeatMapLeadService;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var HeatMapLeadSearch $searchModel */
/* @var yii\widgets\ActiveForm $form */
/* @var yii\data\ActiveDataProvider $dataProvider */
/* @var array $result */
/* @var int $maxCnt */
/* @var array $resultByHour */
/* @var int $maxCntByHour */
/* @var array $resultByMonthDay */
/* @var int $maxCntByMonthDay */
?>

<div class="heat-map-lead-index">
    <?php /* TODO::  */ ?>
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
            echo $this->render('_search', [
                'model' => $searchModel,
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <?php if ($result) : ?>
            <div class="md_box">
                <strong>Hour</strong>
                <?php foreach (HeatMapLeadService::generateHourMap() as $value) : ?>
                    <?php $cntByHour = $resultByHour[$value]['cnt'] ?? 0 ?>
                    <?php $alphaHour = HeatMapLeadService::proportionalMap($cntByHour, 0, $maxCntByHour, 0, 0.9) ?>
                    <div class="hour_box" style="background: rgba(255, 0, 0, <?php echo $alphaHour ?>);">
                        <strong>
                            <?php echo $value ?>
                        </strong>
                        <sup>
                            (<?php echo $cntByHour ?: '-' ?>)
                        </sup>
                    </div>
                <?php endforeach ?>
            </div>

            <?php foreach ($result as $keyMonthDay => $hours) : ?>
                <div class="md_box">
                    <?php $date = \DateTimeImmutable::createFromFormat(HeatMapLeadService::MONTH_DAY_FORMAT, $keyMonthDay); ?>
                    <strong><?php echo $date->format('d-M') ?></strong>
                    <?php foreach ($hours as $hour => $cnt) : ?>
                        <?php $alpha = HeatMapLeadService::proportionalMap($cnt, 0, $maxCnt, 0, 0.9) ?>
                        <div class="hour_box" style="background: rgba(255, 0, 0, <?php echo $alpha ?>);">
                            <?php echo $cnt ?: '-' ?>
                        </div>
                    <?php endforeach ?>

                    <?php $cntByMonthDay = $resultByMonthDay[$keyMonthDay]['cnt'] ?? 0 ?>
                    <?php $alphaMonthDay = HeatMapLeadService::proportionalMap($cntByMonthDay, 0, $maxCntByMonthDay, 0, 0.9) ?>
                    <div class="hour_box" style="background: rgba(255, 0, 0, <?php echo $alphaMonthDay ?>);">
                        <strong>
                            <?php echo $cntByMonthDay ?: '-' ?>
                        </strong>
                    </div>
                </div>
            <?php endforeach ?>
        <?php endif ?>
    </div>
</div>

<?php
$css = <<<CSS
    .md_box {
        float: left; 
        width: 50px;
    }
    .hour_box {
        width: 49px; 
        height: 28px; 
        text-align: center; border: 1px solid #ccc; 
        padding-top: 4px; 
        margin-bottom: 1px;
    }
CSS;
$this->registerCss($css);
