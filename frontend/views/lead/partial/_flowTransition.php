<?php

use common\models\Lead;
use common\models\LeadFlow;

/**
 * @var $flightRequestFlow LeadFlow[]
 * @var $this yii\web\View
 */

?>

<div class="sl-events-log">
    <table class="table table-bordered table-hover table-striped">
        <?php if (!empty($flightRequestFlow)) : ?>
            <thead>
            <tr>
                <th class="text-center" style="width: 40px">Nr</th>
                <th class="text-center">From Status</th>
                <th class="text-center">To Status</th>
                <th class="text-center">Owner</th>
                <th class="text-center">Created</th>
                <th class="text-center">Duration</th>
                <th class="text-center">Description</th>
            </tr>
            </thead>
        <?php endif; ?>
        <tbody>
        <?php if (!empty($flightRequestFlow)) :
            foreach ($flightRequestFlow as $nr => $item) : ?>
                <tr>
                    <td>
                        <?=($nr + 1)?>
                    </td>
                    <td class="text-center">
                        <?= $item->lf_from_status_id ? Lead::getStatusLabel($item->lf_from_status_id) : '-'?><br>
                        <?= $item->created ? Yii::$app->formatter->asDatetime(strtotime($item->created)) : '-' ?>
                    </td>
                    <td class="text-center">
                        <?= Lead::getStatusLabel($item->status) ?><br>
                        <?= $item->lf_end_dt ? Yii::$app->formatter->asDatetime(strtotime($item->lf_end_dt)) : '-' ?>
                    </td>
                    <td> <?= $item->owner ? '<span class="fa fa-user"></span> ' . $item->owner->username : '' ?></td>
                    <td> <?= $item->employee ? '<span class="fa fa-user"></span> ' . $item->employee->username : 'System' ?></td>
                    <td><?php
                        if ($item->lf_time_duration !== null) {
                            echo Yii::$app->formatter->asDuration($item->lf_time_duration);
                        } else {
                            echo Yii::$app->formatter->asDuration(time() - strtotime($item->created));
                        }
                        ?>
                    </td>
                    <td><?= $item->lf_description ? nl2br(\yii\helpers\Html::encode($item->lf_description)) : '-' ?></td>
                </tr>
            <?php endforeach;
        else : ?>
            <tr>
                <th class="text-bold text-center">Not found info for this request!</th>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
