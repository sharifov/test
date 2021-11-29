<?php

/* @var $this View */

use common\helpers\LogHelper;
use sales\helpers\app\ReleaseVersionHelper;
use yii\web\View;

$memoryUsage = Yii::$app->formatter->asShortSize(memory_get_usage());
$memoryPeakUsage = Yii::$app->formatter->asShortSize(memory_get_peak_usage());
$gitBranch = LogHelper::getGitBranch();
?>
<!-- footer content -->
<footer>

    <p class="pull-left">&copy; <?=Yii::$app->name ?> <?= date('Y') ?>,
        <span title="<?=\yii\helpers\Html::encode($gitBranch)?>">
                    v. <?php echo ReleaseVersionHelper::getReleaseVersion(true) ?? '' ?>
                </span>
        <span title="Hostname, Memory usage/peak: <?=$memoryUsage?> / <?=$memoryPeakUsage?>">
                    , host: <?=Yii::$app->params['appHostname'] ?? ''?>
                </span>
    </p>
    <p class="pull-right"><small><i><?=date('Y-m-d H:i:s')?></i></small></p>
    <div class="clearfix"></div>
</footer>
<!-- /footer content -->