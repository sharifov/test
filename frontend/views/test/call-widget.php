<?php

/* @var $this yii\web\View */

$this->title = 'Test Page - Call Widget';
$this->params['breadcrumbs'][] = $this->title;
?>
    <h1><?=\yii\helpers\Html::encode($this->title)?></h1>
    <?= frontend\widgets\CallWidget::widget() ?>