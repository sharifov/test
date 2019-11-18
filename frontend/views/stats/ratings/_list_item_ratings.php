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
            <?=Html::encode($model['username'])?>
        </div>
        <div class="col-md-5">
            <?php if (isset($model['finalProfit']) && $model['finalProfit'] > 0) : ?>
                <?= '$ ' . number_format($model['finalProfit']) ?>
            <?php elseif (isset($model['soldLeads'])  && $model['soldLeads'] > 0 ) :?>
                <?= $model['soldLeads'] ?>
            <?php elseif (isset($model['profitPerPax'])  && $model['profitPerPax'] > 0 ) :?>
                <?= '$ ' . number_format($model['profitPerPax']) ?>
            <?php elseif (isset($model['tips'])  && $model['tips'] > 0 ) :?>
                <?= '$ ' . number_format($model['tips'])?>
            <?php elseif (isset($model['leadConversion']) && $model['leadConversion'] > 0) :?>
                <?= Yii::$app->formatter->asPercent($model['leadConversion']) ?>
                <?= '&nbsp;&nbsp;'.'[' . number_format($model['leadsToProcessing']) .' / ' . number_format($model['leadsWithoutRTS']) .']' ?>
            <?php else:?>
                <?= '-' ?>
            <?php endif;?>
        </div>
    </div>
</div>

