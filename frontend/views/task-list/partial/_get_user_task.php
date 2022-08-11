<?php

use modules\taskList\src\entities\userTask\UserTask;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $userTask UserTask */
/* @var $userTimeZone string */

$hours = round($userTask->taskList->tl_duration_min / 60) ?: 1;
$tsStartUtc = strtotime($userTask->ut_start_dt);
$tsEndUtc = $userTask->ut_end_dt ? strtotime($userTask->ut_end_dt) : ($userTask->taskList->tl_duration_min ? (strtotime($userTask->ut_start_dt) + $userTask->taskList->tl_duration_min * 60) : null);

?>
<div class="user-task-view">

    <div class="col-md-9 text-left">
        <h5>
        <span class="Time Line Type">
            <?php echo Html::encode($userTask->taskList->tl_title) ?>,
        </span>
            &nbsp;
            <span title="User">
            <i class="fa fa-user"></i> <?php echo Html::encode($userTask->user->username) ?>
        </span>
        </h5>
    </div>
    <div class="col-md-3 text-right">
        <h6><span title="Status"><?php echo Html::encode(UserTask::getStatusName($userTask->ut_status_id)) ?></span>
        </h6>
    </div>

    <table class="table table-bordered">
        <thead class="thead-dark">
        <tr class="text-center">
            <th scope="col">Start</th>
            <th scope="col">Duration</th>
            <th scope="col">End</th>
        </tr>
        </thead>
        <tbody>
        <tr class="text-center">
            <td>
                <h6><?= Yii::$app->formatter->asDateTimeByUserTimezone($tsStartUtc, $userTimeZone, 'php: d-M-Y') ?>
                </h6>
                <h4>
                    <i class="fa fa-clock-o"></i> <?= Yii::$app->formatter->asDateTimeByUserTimezone($tsStartUtc, $userTimeZone, 'php: H:i') ?>
                </h4>
            </td>
            <td style="width: 400px">
                <div class="table-responsive" style="width: 400px">
                    <table class="table">
                        <thead>
                        <tr style="background: #2a3f54">
                            <?php for ($i = 0; $i < $hours; $i++) : ?>
                                <td style="color: #FFFFFF">
                                    <?= Yii::$app->formatter->asDateTimeByUserTimezone($tsStartUtc + (60 * 60 * $i), $userTimeZone, 'php:H') ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                        </thead>
                    </table>
                </div>
                <strong>
                    Duration
                    Time: <?php echo Yii::$app->formatter->asDuration($userTask->taskList->tl_duration_min * 60) ?>
                </strong>
            </td>
            <td>
                <?php if ($tsEndUtc) : ?>
                    <h6><?= Yii::$app->formatter->asDateTimeByUserTimezone($tsEndUtc, $userTimeZone, 'php: d-M-Y') ?></h6>
                    <h4><i class="fa fa-clock-o"></i> <?= Yii::$app->formatter->asDateTimeByUserTimezone($tsEndUtc, $userTimeZone, 'php: H:i') ?></h4>
                <?php endif; ?>
            </td>
        </tr>
        </tbody>
    </table>

    <div class="col-md-6 text-left">
        <span title="TimeZone">
            <i class="fa fa-globe"></i> <?= Yii::$app->formatter->timeZone ?>
        </span>
    </div>

    <div class="col-md-6 text-right">
        <span title="Created">
            <i class="fa fa-calendar"></i>
            <?= Yii::$app->formatter->asDatetime(strtotime($userTask->ut_created_dt)) ?>
        </span>
    </div>

    <div class="col-md-12">
        <?php if ($userTask->ut_description) : ?>
            <div title="<?= $userTask->ut_description ?>" style="cursor: pointer">
                Note: <?= Html::encode(\yii\helpers\StringHelper::truncate($userTask->ut_description, 15)) ?></div>
        <?php endif; ?>
    </div>

</div>
