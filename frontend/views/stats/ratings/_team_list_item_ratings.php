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
        <div class="col-md-1">
            <?=($index + 1)?>.
        </div>
        <div class="col-md-4">
            <?/*=Html::tag('i', '', ['class' => 'fa ' . $iconClass . ' fa-lg ' . $class, 'title' => $model['username']])*/?>
            <?=Html::encode($model['ug_name'])?>
        </div>
        <div class="col-md-4">
            <?php if (isset($model['teamsProfit'])) : ?>
                <?= '$ ' . number_format($model['teamsProfit']) ?>
            <?php elseif (isset($model['teamsSoldLeads'])) :?>
                <?= $model['teamsSoldLeads'] != 0 ? number_format($model['teamsSoldLeads']) : null ?>
            <?php elseif (isset($model['teamsProfitPerPax'])) :?>
                <?= '$ ' . number_format($model['teamsProfitPerPax']) ?>
            <?php elseif (isset($model['teamsProfitPerAgent'])) : ?>
                <?= '$ ' . number_format($model['teamsProfitPerAgent'])?>
            <?php elseif (isset($model['teamsConversion'])) : ?>
                <?= Yii::$app->formatter->asPercent($model['teamsConversion']) ?>
            <?php endif;?>
        </div>
    </div>
</div>