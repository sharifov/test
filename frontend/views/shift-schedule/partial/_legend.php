<?php

use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $scheduleTypes ShiftScheduleType[] */
$n = 1;
?>
<div class="shift-schedule-event-view">
    <?php
    if ($scheduleTypes) :
        ?>
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
            <tr class="text-center">
                <th>Nr.</th>
                <th>Color</th>
                <th>Key</th>
                <th>Type Name</th>
                <th>Subtype</th>
            </tr>
            </thead>
        <?php foreach ($scheduleTypes as $item) : ?>
            <tr>
                <td>
                    <?= $n++; ?>
                </td>
                <td>
                    <?= $item->getColorLabel() ?>
                </td>
                <td>
                    <span class="label label-default">
                        <?= Html::encode($item->sst_key) ?>
                    </span>
                </td>
                <td>
                    <?= $item->getIconLabel() ?> &nbsp;
                    <?= Html::encode($item->sst_name) ?>
                </td>
                <td>
                    <?= Html::encode($item->getSubtypeName()) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php endif;?>
</div>
