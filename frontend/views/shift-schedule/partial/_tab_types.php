<?php

use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use yii\helpers\Html;

/* @var $this yii\web\View */

/* @var $monthList array */
/* @var $scheduleTypeList ShiftScheduleType[] */
/* @var $scheduleSumData array */

$subtypeTotalData = [];
$totalData = [];
?>
<table class="table table-bordered">
    <thead>
    <tr class="text-center bg-info">
        <th>Type Key</th>
        <th>Type Name</th>
        <th title="Subtype">Subtype</th>
        <?php foreach ($monthList as $month) : ?>
            <th style="font-size: 16px"><?= Html::encode($month)?></th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php if ($scheduleTypeList) : ?>
        <?php foreach ($scheduleTypeList as $item) : ?>
            <tr class="text-center" title="<?= Html::encode($item->sst_title)?>">
                <td title="Type Id: <?= $item->sst_id?>">
                    <span class="label label-default"><?= Html::encode($item->sst_key)?></span>
                </td>
                <td class="text-left">
                    <?= $item->getColorLabel()?> &nbsp;
                    <?= $item->getIconLabel()?> &nbsp;
                    <?= Html::encode($item->sst_name)?>
                </td>
                <td>
                    <?php echo Html::encode($item->getSubtypeName()) ?>
                    <?php /*if ($item->getSubtypeName()) :?>
                                        <i class="fa fa-check-circle"></i>
                                    <?php endif;*/ ?>
                </td>
                <?php foreach ($monthList as $monthId => $month) : ?>
                    <?php /*echo $monthId; \yii\helpers\VarDumper::dump($scheduleSumData[$item->sst_id], 10, true)*/ ?>
                    <td>
                        <?php if (!empty($scheduleSumData[$item->sst_id][$monthId])) :
                            $dataItem = $scheduleSumData[$item->sst_id][$monthId];

                            if ($item->sst_subtype_id) {
                                if (isset($subtypeTotalData[$item->sst_subtype_id][$monthId]['cnt'])) {
                                    $subtypeTotalData[$item->sst_subtype_id][$monthId]['duration'] += $dataItem['uss_duration'];
                                    $subtypeTotalData[$item->sst_subtype_id][$monthId]['cnt'] += $dataItem['uss_cnt'];
                                } else {
                                    $subtypeTotalData[$item->sst_subtype_id][$monthId]['duration'] = $dataItem['uss_duration'];
                                    $subtypeTotalData[$item->sst_subtype_id][$monthId]['cnt'] = $dataItem['uss_cnt'];
                                }
                            }

                            if (isset($totalData[$monthId]['cnt'])) {
                                $totalData[$monthId]['duration'] += $dataItem['uss_duration'];
                                $totalData[$monthId]['cnt'] += $dataItem['uss_cnt'];
                            } else {
                                $totalData[$monthId]['duration'] = $dataItem['uss_duration'];
                                $totalData[$monthId]['cnt'] = $dataItem['uss_cnt'];
                            }
                            ?>

                            <?php echo $this->render('__format_duration', [
                                    'duration' => $dataItem['uss_duration'],
                                    'count' => $dataItem['uss_cnt']
                                ])
                            ?>



                        <?php else : ?>
                            -
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="6"></td>
    </tr>

    <?php if ($subtypeTotalData) : ?>
        <?php foreach ($subtypeTotalData as $subtypeId => $dataItem) : ?>
            <tr class="text-center">
                <th></th>
                <th class="text-right">
                    Total "<?php echo Html::encode(ShiftScheduleType::getSubtypeNameById($subtypeId))?>"
                </th>
                <th></th>
                <?php foreach ($monthList as $monthId => $month) : ?>
                    <th>
                        <?php echo $this->render('__format_duration', [
                            'duration' => $dataItem[$monthId]['duration'] ?? 0,
                            'count' => $dataItem[$monthId]['cnt'] ?? 0
                        ])
                        ?>
                    </th>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>

    <tr class="text-center">
        <th></th>
        <th class="text-right">Total Hours</th>
        <th></th>
        <?php foreach ($monthList as $monthId => $month) : ?>
            <th>
                <?php echo $this->render('__format_duration', [
                        'duration' => $totalData[$monthId]['duration'] ?? 0,
                        'count' => $totalData[$monthId]['cnt'] ?? 0
                    ])
                ?>
            </th>
        <?php endforeach; ?>
    </tr>

    </tfoot>
</table>