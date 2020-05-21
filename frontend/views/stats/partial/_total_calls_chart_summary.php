<?php

/**
 * @var $totalCallsDbData array
 */

$totalIncomingCalls = $totalIncomingCallsAvg = $totalIncomingDuration = $totalIncomingDurationAvg = 0;
$totalOutgoingCalls = $totalOutgoingCallsAvg = $totalOutgoingDuration = $totalOutgoingDurationAvg = 0;
$totalInOutCalls = $totalInOutCallsAvg = $totalInOutCallsDuration = $totalInOutCallsDurationAvg = 0;

$countIn = $countOut = $countInOut = 0;

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
    if ($results['callType'] === 'total'){
        $countInOut++;
        $totalInOutCalls = $totalInOutCalls + $results['totalCalls'];
        $totalInOutCallsAvg = $totalInOutCallsAvg + $results['avgCallsPerGroup'];
        $totalInOutCallsDuration = $totalInOutCallsDuration + $results['totalCallsDuration'];
        $totalInOutCallsDurationAvg = $totalInOutCallsDurationAvg + $results['avgCallDuration'];
    }
}

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
                <td><?= number_format($totalIncomingCallsAvg / ($countIn ?: 1)) ?></td>
                <td><?= Yii::$app->formatter->asDuration($totalIncomingDuration) ?></td>
                <td><?= Yii::$app->formatter->asDuration((int)$totalIncomingDurationAvg) ?></td>
            </tr>
            <tr>
                <td>Outgoing</td>
                <td><?= $totalOutgoingCalls ?></td>
                <td><?= number_format($totalOutgoingCallsAvg / ($countOut ?: 1)) ?></td>
                <td><?= Yii::$app->formatter->asDuration((int)$totalOutgoingDuration) ?></td>
                <td><?= Yii::$app->formatter->asDuration((int)$totalOutgoingDurationAvg) ?></td>
            </tr>
            <tr>
                <td>Total</td>
                <td><?= $totalInOutCalls ?></td>
                <td><?= number_format($totalInOutCallsAvg / ($countInOut ?: 1)) ?></td>
                <td><?= Yii::$app->formatter->asDuration((int)$totalInOutCallsDuration) ?></td>
                <td><?= Yii::$app->formatter->asDuration((int)$totalInOutCallsDurationAvg) ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
