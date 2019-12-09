<?php
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $index int
 */
$iconClass = 'fa-user';
$class = 'text-success';
?>

<div class="col-md-12" style="margin-bottom: 5px">
    <div class="list-striped">
        <div class="col-md-2">
            <?=($index + 1)?>.
        </div>
        <div class="col-md-6">
            <?/*=Html::tag('i', '', ['class' => 'fa ' . $iconClass . ' fa-lg ' . $class, 'title' => $model['username']])*/?>
            <?=Html::encode($model['ug_name'])?>
        </div>
        <div class="col-md-3">
            <?php if (isset($model['teamsProfit']) && $model['teamsProfit'] > 0) : ?>
                <?= '$ ' . number_format($model['teamsProfit']) ?>
            <?php elseif (isset($model['teamsSoldLeads']) && $model['teamsSoldLeads'] > 0) :?>
                <?= number_format($model['teamsSoldLeads'], 1) ?>
            <?php elseif (isset($model['teamsProfitPerPax']) && $model['teamsProfitPerPax'] > 0) :?>
                <?= '$ ' . number_format($model['teamsProfitPerPax']) ?>
            <?php elseif (isset($model['teamsProfitPerAgent']) && $model['teamsProfitPerAgent'] > 0) : ?>
                <?= '$ ' . number_format($model['teamsProfitPerAgent'])?>
            <?php elseif (isset($model['teamsConversion']) && $model['teamsConversion'] > 0) : ?>
                <?= Yii::$app->formatter->asPercent($model['teamsConversion']) ?>
                <?= '&nbsp;&nbsp;'.'[' . number_format($model['teamLeadsToProcessing']) .' / ' . number_format($model['teamLeadsWithoutRTS']) .']' ?>
            <?php else:?>
                <?= '-' ?>
            <?php endif;?>
        </div>
    </div>
</div>