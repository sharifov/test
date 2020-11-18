<?php
/**
 * @var $viewModel \sales\viewModel\chat\ViewModelChatExtendedGraph
 */
$amountOfChats = $viewModel->amountOfChats;
?>

<div class="row" style="margin-top: 40px;">
    <div class="col-md-12">
        <!--<p>Summary</p>-->
        <table class="table table-striped table-bordered detail-view">
            <tbody>
            <tr>
                <td colspan="6" align="center"><strong>Summary Statistics</strong></td>
                <!-- <td colspan="2" align="center" width="50%">Number Of Chats</td>
                 <td colspan="2" align="center" width="50%">Reaction Duration</td>-->
            </tr>
            <tr>
                <td></td>
                <td>Amount of Chats</td>
                <td>Amount of Accepted Chats</td>
                <td>Amount of Missed Chats</td>
                <td>First Response Time (FRT)</td>
                <td>Average Chat Duration (ACD) <i class="fa fa-exclamation-triangle" aria-hidden="true" title="Calculated by Chats in Statuses Closed/Archive"></i></td>
            </tr>
            <tr>
                <td>Incoming</td>
                <td> <?= $amountOfChats['clients'] ?> </td>
                <td> <?= $amountOfChats['acceptedByAgentSourceAgent'] ?> </td>
                <td> - </td>
                <td> <?= $amountOfChats['totalFrtAvg'] ?> </td>
                <td> <?= $amountOfChats['totalClientChatDurationAvg'] ?> </td>
            </tr>
            <tr>
                <td>Outgoing</td>
                <td><?= $amountOfChats['agents'] ?></td>
                <td> <?= $amountOfChats['acceptedByAgentSourceClient'] ?> </td>
                <td> - </td>
                <td> - </td>
                <td> <?= $amountOfChats['totalAgentChatDurationAvg'] ?> </td>
            </tr>
            <tr>
                <td>Total</td>
                <td><?= $amountOfChats['total'] ?></td>
                <td><?= $amountOfChats['acceptedByAgentSourceAgent'] + $amountOfChats['acceptedByAgentSourceClient'] ?></td>
                <td><?= $amountOfChats['missedChats'] ?></td>
                <td><?= $amountOfChats['totalFrtAvg'] ?></td>
                <td> <?= $amountOfChats['totalChatDurationAvg'] ?> </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
