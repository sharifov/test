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
    <div class="row">
        <div class="col-md-1">
            <?=($index + 1)?>.
        </div>
        <div class="col-md-3">
            <?/*=Html::tag('i', '', ['class' => 'fa ' . $iconClass . ' fa-lg ' . $class, 'title' => $model['username']])*/?>
            <?=Html::encode($model['username'])?>
        </div>
        <div class="col-md-4">
            <?php if (isset($model['finalProfit'])) : ?>
                <?= Yii::$app->formatter->asCurrency((float)$model['finalProfit'], 'USD') ?>
            <?php elseif (isset($model['soldLeads'])) :?>
                <?= $model['soldLeads'] ?>
            <?php elseif (isset($model['profitPerPax'])) :?>
                <?= Yii::$app->formatter->asCurrency((float)$model['profitPerPax'], 'USD') ?>
            <?php elseif (isset($model['tips'])) :?>
                <?= Yii::$app->formatter->asCurrency($model['tips'], 'USD') ?>
            <?php endif;?>
        </div>
    </div>
</div>

