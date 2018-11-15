<?php
use yii\helpers\Html;
/* @var $this yii\web\View */

$this->title = 'Framework Cache Cleaner';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clean-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('cleaner' )): ?>
        <?php foreach ((array)Yii::$app->session->getFlash('cleaner', []) as $message):?>
            <div class="alert alert-success">
                <?= $message ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <br>
    <p>
        <?= Html::a('Clear Caches & schema refresh', ['cache'], [
            'class' => 'btn btn-info',
            'data' => [
                'confirm' => 'Are you sure you want to clear all cache data?', 'method ' => 'post',
            ],
        ]) ?>
        <?= Html::a('Clear Assets', ['assets'],
            ['class' => 'btn btn-primary',
                'data' => [
                    'confirm' => 'Are you sure you want to clear all temporary assets?',
                    'method ' => 'post',
                ],
            ]) ?>
        <?= Html::a('Clear Runtime', ['runtime'],
            ['class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Are you sure you want to clear all runtime files?',
                    'method ' => 'post',
                ],
            ]) ?>
    </p>
</div>