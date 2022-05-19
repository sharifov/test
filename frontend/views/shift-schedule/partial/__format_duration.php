<?php
/* @var $this yii\web\View */
/* @var $duration int */
/* @var $count int */
?>
<span title="Time Duration: <?php echo \Yii::$app->formatter->asDuration($duration * 60) ?>" data-toggle="tooltip">
    <i class="fa fa-clock-o"></i> <?= $duration ?
    \Yii::$app->formatter->asHoursDuration((int) $duration) : '-'?>
</span>
<span title="Count of Schedule Events" data-toggle="tooltip" style="margin-left: 7px">
    <i class="fa fa-bookmark-o"></i>
    <?= $count ?: '-'?>
</span>
