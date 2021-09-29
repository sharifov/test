<?php

/**
 * @var $totalCallsDbData array
 * @var $groupsCount int
 */

$totalIncomingCalls = $totalIncomingCallsAvg = $totalIncomingDuration = 0;
$totalOutgoingCalls = $totalOutgoingCallsAvg = $totalOutgoingDuration = 0;
$totalInOutCalls = $totalInOutCallsAvg = $totalInOutCallsDuration = 0;

foreach ($totalCallsDbData as $results) {
    if ($results['callType'] === 'in') {
        $totalIncomingCalls = $totalIncomingCalls + $results['totalCalls'];
        //$totalIncomingCallsAvg = $totalIncomingCallsAvg + $results['avgCallsPerGroup'];
        $totalIncomingDuration = $totalIncomingDuration + $results['totalCallsDuration'];
    }
    if ($results['callType'] === 'out') {
        $totalOutgoingCalls = $totalOutgoingCalls + $results['totalCalls'];
        //$totalOutgoingCallsAvg = $totalOutgoingCallsAvg + $results['avgCallsPerGroup'];
        $totalOutgoingDuration = $totalOutgoingDuration + $results['totalCallsDuration'];
    }
    if ($results['callType'] === 'total') {
        $totalInOutCalls = $totalInOutCalls + $results['totalCalls'];
        //$totalInOutCallsAvg = $totalInOutCallsAvg + $results['avgCallsPerGroup'];
        $totalInOutCallsDuration = $totalInOutCallsDuration + $results['totalCallsDuration'];
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
                <td><?= number_format($totalIncomingCalls /  $groupsCount) ?></td>
                <td><?= Yii::$app->formatter->asDuration($totalIncomingDuration) ?></td>
                <td><?= Yii::$app->formatter->asDuration((int)$totalIncomingDuration / ($totalIncomingCalls ?: 1)) ?></td>
            </tr>
            <tr>
                <td>Outgoing</td>
                <td><?= $totalOutgoingCalls ?></td>
                <td><?= number_format($totalOutgoingCalls / $groupsCount) ?></td>
                <td><?= Yii::$app->formatter->asDuration((int)$totalOutgoingDuration) ?></td>
                <td><?= Yii::$app->formatter->asDuration((int)$totalOutgoingDuration / ($totalOutgoingCalls ?: 1)) ?></td>
            </tr>
            <tr>
                <td>Total</td>
                <td><?= $totalInOutCalls ?></td>
                <td><?= number_format($totalInOutCalls /  $groupsCount) ?></td>
                <td><?= Yii::$app->formatter->asDuration((int)$totalInOutCallsDuration) ?></td>
                <td><?= Yii::$app->formatter->asDuration((int)$totalInOutCallsDuration / ($totalInOutCalls ?: 1)) ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
