<?php

use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $event UserShiftSchedule */

$hours = round($event->uss_duration / 60) ?: 1;
$tsStartUtc = strtotime($event->uss_start_utc_dt);
$tsEndUtc = strtotime($event->uss_end_utc_dt);

?>
<div class="shift-schedule-event-view">

    <div class="col-md-9 text-left">
    <h5>
        <span class="Time Line Type">
            <?php echo $event->shiftScheduleType->getIconLabel()?>
            <?php echo Html::encode($event->getScheduleTypeTitle())?>,
        </span>
        &nbsp;
        <span title="User">
            <i class="fa fa-user"></i> <?php echo Html::encode($event->user->username)?>
        </span>
    </h5>
    </div>
    <div class="col-md-3 text-right">
        <h6><span title="Status"><?php echo Html::encode($event->getStatusName())?></span></h6>
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
                    <h6><?= Yii::$app->formatter->asDatetime($tsStartUtc, 'php: d-M-Y')?></h6>
                    <h4><i class="fa fa-clock-o"></i> <?= Yii::$app->formatter->asTime($tsStartUtc)?></h4>
                </td>
                <td style="width: 400px">
                    <div class="table-responsive" style="width: 400px">
                        <table class="table">
                            <thead>
                            <tr style="background: <?=Html::encode($event->shiftScheduleType->sst_color)?>">
                                <?php for ($i = 0; $i < $hours; $i++) : ?>
                                <td style="color: #FFFFFF">
                                    <?= Yii::$app->formatter->asDatetime($tsStartUtc + (60 * 60 * $i), 'php:H') ?>
                                </td>
                                <?php endfor; ?>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <strong>
                        Duration Time: <?php echo Yii::$app->formatter->asDuration($event->uss_duration * 60) ?>
                    </strong>
                </td>
                <td>
                    <h6><?= Yii::$app->formatter->asDatetime($tsEndUtc, 'php: d-M-Y')?></h6>
                    <h4><i class="fa fa-clock-o"></i> <?= Yii::$app->formatter->asTime($tsEndUtc)?></h4>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="col-md-6 text-left">
        <span title="TimeZone">
            <i class="fa fa-globe"></i> <?= Yii::$app->formatter->timeZone?>
        </span>
    </div>

    <div class="col-md-6 text-right">
        <span title="Created / Updated">
            <i class="fa fa-calendar"></i>
            <?= Yii::$app->formatter->asDatetime(strtotime($event->uss_updated_dt ?: $event->uss_created_dt))?>
        </span>
    </div>

    <div class="col-md-12">
        <?php if ($event->uss_description) : ?>
          <div>Description: <?= Html::encode($event->uss_description) ?></div>
        <?php endif; ?>
    </div>

</div>
