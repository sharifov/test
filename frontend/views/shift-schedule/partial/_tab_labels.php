<?php

use modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $monthList array */
/* @var $scheduleTypeLabelList ShiftScheduleTypeLabel[] */
/* @var $scheduleLabelSumData array */

$totalData = [];
?>
<table class="table table-bordered">
    <thead>
    <tr class="text-center bg-info">
        <th>Label Key</th>
        <th>Label Name</th>
        <th></th>
        <?php foreach ($monthList as $month) : ?>
            <th style="font-size: 16px"><?= Html::encode($month)?></th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php if ($scheduleTypeLabelList) : ?>
        <?php foreach ($scheduleTypeLabelList as $item) : ?>
            <tr class="text-center" title="<?= Html::encode($item->stl_name)?>">
                <td title="Label key: <?= $item->stl_key?>">
                    <span class="label label-default"><?= Html::encode($item->stl_key)?></span>
                </td>
                <td class="text-left">
                    <?php /*= $item->getColorLabel()*/?>
                    <?= $item->getIconLabel()?> &nbsp;
                    <?= Html::encode($item->stl_name)?>
                </td>
                <td>
                </td>
                <?php foreach ($monthList as $monthId => $month) : ?>
                    <?php /*echo $monthId; \yii\helpers\VarDumper::dump($scheduleLabelSumData[$item->sst_id], 10, true)*/ ?>
                    <td>
                        <?php if (!empty($scheduleLabelSumData[$item->stl_key][$monthId])) :
                            $dataItem = $scheduleLabelSumData[$item->stl_key][$monthId];

//                            if (isset($totalData[$monthId]['cnt'])) {
//                                $totalData[$monthId]['duration'] += $dataItem['uss_duration'];
//                                $totalData[$monthId]['cnt'] += $dataItem['uss_cnt'];
//                            } else {
//                                $totalData[$monthId]['duration'] = $dataItem['uss_duration'];
//                                $totalData[$monthId]['cnt'] = $dataItem['uss_cnt'];
//                            }
                            ?>

                            <?= round($dataItem['uss_duration'] / 60, 1)?>h
                            / <?= Html::encode($dataItem['uss_cnt'])?>

                        <?php else : ?>
                            -
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
<!--    <tfoot>-->
<!--    <tr>-->
<!--        <td colspan="6"></td>-->
<!--    </tr>-->
<!---->
<!---->
<!--    <tr class="text-center">-->
<!--        <th></th>-->
<!--        <th class="text-right">Total Hours</th>-->
<!--        <th></th>-->
<!--        --><?php //foreach ($monthList as $monthId => $month) : ?>
<!--            <th>-->
<!--                --><?//= isset($totalData[$monthId]['duration']) ? round($totalData[$monthId]['duration'] / 60, 1) . 'h' : '-'?><!-- /-->
<!--                --><?//= isset($totalData[$monthId]['cnt']) ? ($totalData[$monthId]['cnt']) : '-'?>
<!--            </th>-->
<!--        --><?php //endforeach; ?>
<!--    </tr>-->
<!---->
<!--    </tfoot>-->
</table>