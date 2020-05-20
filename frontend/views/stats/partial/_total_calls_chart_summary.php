<?php

use yii\helpers\ArrayHelper;

/**
 * @var $totalCallsDbData array
 */

/*$totalRows = count($totalCallsDbData) ?: 1;
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
$totalRecDurationAvg = $totalRecDuration / (($totalIncomingCalls + $totalOutgoingCalls) ?: 1);*/

$totalIncomingCalls = $totalIncomingCallsAvg = $totalIncomingDuration = $totalIncomingDurationAvg = 0;
$totalOutgoingCalls = $totalOutgoingCallsAvg = $totalOutgoingDuration = $totalOutgoingDurationAvg = 0;

$countIn = $countOut = 0;

foreach ($totalCallsDbData as $results){
    if ($results['callType'] === 'in'){
        $countIn++;
        $totalIncomingCalls = $totalIncomingCalls + $results['totalCalls'];
        $totalIncomingCallsAvg = $totalIncomingCallsAvg + $results['avgCallsPerGroup'];
        $totalIncomingDuration = $totalIncomingDuration + $results['totalCallsDuration'];
        $totalIncomingDurationAvg = $totalIncomingDurationAvg + $results['avgCallDuration'];
    }
    if ($results['callType'] === 'out'){
        $countOut++;
        $totalOutgoingCalls = $totalOutgoingCalls + $results['totalCalls'];
        $totalOutgoingCallsAvg = $totalOutgoingCallsAvg + $results['avgCallsPerGroup'];
        $totalOutgoingDuration = $totalOutgoingDuration + $results['totalCallsDuration'];
        $totalOutgoingDurationAvg = $totalOutgoingDurationAvg + $results['avgCallDuration'];
    }
}

$totalCalls = $totalIncomingCalls + $totalOutgoingCalls;
$totalCallsAvg = $totalIncomingCallsAvg + $totalOutgoingCallsAvg;
$totalCallsDuration = $totalIncomingDuration + $totalOutgoingDuration;
$totalCallsDurationAvg = $totalIncomingDurationAvg + $totalOutgoingDurationAvg;

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
                <td><?= number_format($totalIncomingCallsAvg / $countIn) ?></td>
                <td><?= Yii::$app->formatter->asDuration($totalIncomingDuration) ?></td>
                <td><?= Yii::$app->formatter->asDuration((int)$totalIncomingDurationAvg) ?></td>
            </tr>
            <tr>
                <td>Outgoing</td>
                <td><?= $totalOutgoingCalls ?></td>
                <td><?= number_format($totalOutgoingCallsAvg / $countOut) ?></td>
                <td><?= Yii::$app->formatter->asDuration((int)$totalOutgoingDuration) ?></td>
                <td><?= Yii::$app->formatter->asDuration((int)$totalOutgoingDurationAvg) ?></td>
            </tr>
            <tr>
                <td>Total</td>
                <td><?= $totalCalls ?></td>
                <td><?= number_format($totalCallsAvg / (($countIn + $countOut) / 2)) ?></td>
                <td><?= Yii::$app->formatter->asDuration((int)$totalCallsDuration) ?></td>
                <td><?= Yii::$app->formatter->asDuration((int)$totalCallsDurationAvg / (($countIn + $countOut) / 2)) ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
