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

$this->title = 'Heat Map Lead';
$this->params['breadcrumbs'][] = $this->title;
$rgbaTitle = '151, 149, 149, 0.1';
?>

<div class="heat-map-lead-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (!$searchModel->validate()) : ?>
        <div class="js_error_box alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo \src\helpers\ErrorsToStringHelper::extractFromModel($searchModel) ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif ?>

    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Heat Map Lead Search</h2>
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

    <div class="row" style="margin-left: 2px; margin-top: 18px;">
        <?php if ($maxCnt) : ?>
            <div class="map_box">
                <div class="md_box">
                    <div class="title_box">
                        <div class="title_row"></div>
                        <div class="title_row"></div>
                        <div class="title_row_last">
                            <strong>Hour</strong>
                        </div>
                    </div>
                    <?php foreach (HeatMapLeadService::generateHourMap() as $value) : ?>
                        <div class="hour_box" style="background: rgba(<?php echo $rgbaTitle ?>);">
                            <strong>
                                <?php echo $value ?>
                            </strong>
                        </div>
                    <?php endforeach ?>
                </div>

                <div class="md_box">
                    <div class="title_box">
                        <div class="title_row"></div>
                        <div class="title_row"></div>
                        <div class="title_row_last">
                            <strong>Count</strong>
                        </div>
                    </div>
                    <?php foreach (HeatMapLeadService::generateHourMap() as $value) : ?>
                        <?php $cntByHour = $resultByHour[$value]['cnt'] ?? 0 ?>
                        <?php $alphaHour = HeatMapLeadService::proportionalMap($cntByHour, 0, $maxCntByHour, 0, 0.9) ?>
                        <?php $backgroundHour = $cntByHour ? '255, 0, 0, ' . $alphaHour : $rgbaTitle ?>
                        <div class="hour_box" style="background: rgba(<?php echo $backgroundHour ?>);">
                            <span>
                                <?php echo $cntByHour ?: '-' ?>
                            </span>
                        </div>
                    <?php endforeach ?>
                </div>

                <?php $prevMonth = null ?>
                <?php foreach ($result as $keyMonthDay => $hours) : ?>
                    <div class="md_box">
                        <?php $date = \DateTimeImmutable::createFromFormat(HeatMapLeadService::MONTH_DAY_FORMAT, $keyMonthDay); ?>
                        <?php $month = $date->format('M') ?>
                        <div class="title_box">
                            <div class="title_row">
                                <?php if ($month !== $prevMonth) : ?>
                                    <strong><?php echo $month ?></strong>
                                    <?php $prevMonth = $month ?>
                                <?php endif ?>
                            </div>
                            <div class="title_row">
                                <?php echo $date->format('D') ?>
                            </div>
                            <div class="title_row_last">
                                <strong><?php echo $date->format('d') ?></strong>
                            </div>
                        </div>

                        <?php foreach ($hours as $hour => $cnt) : ?>
                            <?php $dataTitle = $date->format('d-M') . ' (' . $hour . ':00 - ' . $hour . ':59)' ?>
                            <?php $cell = Html::tag(
                                'span',
                                $cnt ?: '-',
                                [
                                    'class' => '', 'data-toggle' => 'tooltip', 'data-original-title' => $dataTitle
                                ]
                            ) ?>
                            <?php $alpha = HeatMapLeadService::proportionalMap($cnt, 0, $maxCnt, 0, 0.9) ?>
                            <div class="hour_box" style="background: rgba(255, 0, 0, <?php echo $alpha ?>);">
                                <?php echo $cell ?>
                            </div>
                        <?php endforeach ?>

                        <?php $cntByMonthDay = $resultByMonthDay[$keyMonthDay]['cnt'] ?? 0 ?>
                        <?php $alphaMonthDay = HeatMapLeadService::proportionalMap($cntByMonthDay, 0, $maxCntByMonthDay, 0, 0.9) ?>
                        <?php $backgroundMonthDay = $cntByMonthDay ? '255, 0, 0, ' . $alphaMonthDay : $rgbaTitle ?>
                        <div class="hour_box" style="background: rgba(<?php echo $backgroundMonthDay ?>);">
                            <strong>
                                <?php echo $cntByMonthDay ?: '-' ?>
                            </strong>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
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
        width: 50px; 
        height: 28px; 
        text-align: center; 
        border: 1px solid #ccc; 
        padding-top: 4px; 
        margin-bottom: 1px;
    }
    .title_box {
        display: inline-block;
        width: 50px; 
        height: 60px;
        text-align: center; 
        border: 1px solid #ccc; 
        margin-bottom: 1px;
        background: rgba({$rgbaTitle});
    } 
    .title_row, .title_row_last {
        text-align: center; 
        height: 18px;
    }
    .title_row_last {
         
    }
CSS;
$this->registerCss($css);
