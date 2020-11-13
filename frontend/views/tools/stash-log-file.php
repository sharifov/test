<?php

use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $lines int */
/* @var $frontendData string */
/* @var $consoleData string */
/* @var $webapiData string */

/* @var $fnFrontend string */
/* @var $fnConsole string */
/* @var $fnWebapi string */

$this->title = 'Stash log files';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stash-log-file">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['timeout' => 8000]); ?>

    <p>
        <?= Html::a('<i class="fa fa-refresh"></i> Refresh data', ['tools/stash-log-file'], ['class' => 'btn btn-warning']) ?>

    </p>
    <p>
        Last load: <i class="fa fa-clock-o"></i> <?=Yii::$app->formatter->asTime(time(), 'php: H:i:s')?>
    </p>

    <h2>Frontend stash log file (last <?=$lines?> lines):</h2>
    <i>File: <?=Html::encode($fnFrontend)?></i>
    <pre><?= Html::encode($frontendData) ?></pre>

    <h2>Console stash log file (last <?=$lines?> lines):</h2>
    <i>File: <?=Html::encode($fnConsole)?></i>
    <pre><?= Html::encode($consoleData) ?></pre>

    <h2>WebApi stash log file (last <?=$lines?> lines):</h2>
    <i>File: <?=Html::encode($fnWebapi)?></i>
    <pre><?= Html::encode($webapiData) ?></pre>
    <?php Pjax::end(); ?>
</div>