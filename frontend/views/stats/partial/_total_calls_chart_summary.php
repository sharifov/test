<?php

use yii\helpers\ArrayHelper;

/**
 * @var $totalCallsDbData array
 */

$totalRows = count($totalCallsDbData) ?: 1;
$inRecDuration = array_sum(ArrayHelper::getColumn($totalCallsDbData, 'inc_dur_count'));
$outRecDuration = array_sum(ArrayHelper::getColumn($totalCallsDbData, 'out_dur_count'));

$totalIncomingCalls = array_sum(ArrayHelper::getColumn($totalCallsDbData, 'incoming'));
$totalIncomingCallsAvg = $totalIncomingCalls / $totalRows;
$totalIncomingRecDuration = array_sum(ArrayHelper::getColumn($totalCallsDbData, 'in_rec_duration'));
$totalIncomingRecDurationAvg = $totalIncomingRecDuration / ($totalIncomingCalls ?: 1);

$totalOutgoingCalls = array_sum(ArrayHelper::getColumn($totalCallsDbData, 'outgoing'));
$totalOutgoingCallsAvg = $totalOutgoingCalls / $totalRows;
$totalOutgoingRecDuration = array_sum(ArrayHelper::getColumn($totalCallsDbData, 'out_rec_duration'));
$totalOutgoingRecDurationAvg = $totalOutgoingRecDuration / ($totalOutgoingCalls ?: 1);

$totalCalls = array_sum(ArrayHelper::getColumn($totalCallsDbData, 'total_calls'));
$totalCallsAvg = $totalCalls / $totalRows;
$totalRecDuration = array_sum(ArrayHelper::getColumn($totalCallsDbData, 'total_rec_duration'));
$totalRecDurationAvg = $totalRecDuration / (($totalIncomingCalls + $totalOutgoingCalls) ?: 1);

?>
<div class="row" style="margin-top: 40px;">
    <div class="col-md-12">
        <p>Summary</p>
        <table class="table table-striped table-bordered detail-view">
            <tbody>
                <tr>
                    <td></td>
                    <td colspan="2" align="center" width="50%">Number Of Calls</td>
                    <td colspan="2" align="center" width="50%">Call Duration</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Total</td>
                    <td>Average</td>
                    <td>Total</td>
                    <td>Average</td>
                </tr>
                <tr>
                    <td>Incoming</td>
                    <td><?= $totalIncomingCalls ?></td>
                    <td><?= number_format($totalIncomingCallsAvg) ?></td>
                    <td><?= Yii::$app->formatter->asDuration($totalIncomingRecDuration) ?></td>
                    <td><?= Yii::$app->formatter->asDuration((int)$totalIncomingRecDurationAvg) ?></td>
                </tr>
                <tr>
                    <td>Outgoing</td>
                    <td><?= $totalOutgoingCalls ?></td>
                    <td><?= number_format($totalOutgoingCallsAvg) ?></td>
                    <td><?= Yii::$app->formatter->asDuration((int)$totalOutgoingRecDuration) ?></td>
                    <td><?= Yii::$app->formatter->asDuration((int)$totalOutgoingRecDurationAvg) ?></td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td><?= $totalCalls ?></td>
                    <td><?= number_format($totalCallsAvg) ?></td>
                    <td><?= Yii::$app->formatter->asDuration((int)$totalRecDuration) ?></td>
                    <td><?= Yii::$app->formatter->asDuration((int)$totalRecDurationAvg) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
