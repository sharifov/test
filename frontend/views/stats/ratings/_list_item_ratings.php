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
        <div class="col-md-3">
            <?/*=Html::tag('i', '', ['class' => 'fa ' . $iconClass . ' fa-lg ' . $class, 'title' => $model['username']])*/?>
            <?=Html::encode($model['username'])?>
        </div>
        <div class="col-md-4">
            <?php if (isset($model['finalProfit'])) : ?>
                <?= '$ ' . number_format($model['finalProfit']) ?>
            <?php elseif (isset($model['soldLeads'])) :?>
                <?= $model['soldLeads'] ?>
            <?php elseif (isset($model['profitPerPax'])) :?>
                <?= '$ ' . number_format($model['profitPerPax']) ?>
            <?php elseif (isset($model['tips'])) :?>
                <?= '$ ' . number_format($model['tips'])?>
            <?php endif;?>
        </div>
    </div>
</div>

