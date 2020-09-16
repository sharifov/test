<?php

use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $lines int */
/* @var $frontendData string */
/* @var $consoleData string */
/* @var $webapiData string */

$this->title = 'Stash log files';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stash-log-file">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>

    <p>
        <?= Html::a('<i class="fa fa-refresh"></i> Refresh', ['tools/stash-log-file'], ['class' => 'btn btn-primary']) ?>

    </p>
    <p>
        <i class="fa fa-clock-o"></i> <?=Yii::$app->formatter->asTime(time(), 'php: H:i:s')?>
    </p>

    <h3>Frontend stash log file (last <?=$lines?> lines):</h3>
    <pre><?= Html::encode($frontendData) ?></pre>

    <h3>Console stash log file (last <?=$lines?> lines):</h3>
    <pre><?= Html::encode($consoleData) ?></pre>

    <h3>WebApi stash log file (last <?=$lines?> lines):</h3>
    <pre><?= Html::encode($webapiData) ?></pre>
    <?php Pjax::end(); ?>
</div>