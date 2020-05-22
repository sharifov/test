<?php
use sales\model\saleTicket\entity\SaleTicket;
use yii\helpers\Html;


/** @var $saleTickets SaleTicket[] */
?>
<table width="100%" cellpadding="0" cellspacing="0" style="min-width:100%;">
    <thead>
        <tr>
            <th style="border: 1px solid; padding: 10px;">Last/First Name</th>
            <th style="border: 1px solid; padding: 10px;">Ticket Number</th>
            <th style="border: 1px solid; padding: 10px;">Record Locator</th>
            <th style="border: 1px solid; padding: 10px;">Original FOP</th>
            <th style="border: 1px solid; padding: 10px;">Charge System</th>
            <th style="border: 1px solid; padding: 10px;">Airline Penalty</th>
            <th style="border: 1px solid; padding: 10px;">Penalty Amount</th>
            <th style="border: 1px solid; padding: 10px;">Selling</th>
            <th style="border: 1px solid; padding: 10px;">Service Fee</th>
            <th style="border: 1px solid; padding: 10px;">Real Commission</th>
            <th style="border: 1px solid; padding: 10px;">Markup</th>
            <th style="border: 1px solid; padding: 10px;">Upfront Charge</th>
            <th style="border: 1px solid; padding: 10px;">Refundable Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $totalUpfrontCharge = 0;
        $totalRefundableAmount = 0;
        foreach($saleTickets as $key => $ticket): ?>
            <tr>
                <td style="border: 1px solid; padding: 10px;"><?=Html::encode($ticket->st_client_name)?></td>
                <td style="border: 1px solid; padding: 10px;"><?=Html::encode($ticket->st_ticket_number)?></td>
                <td style="border: 1px solid; padding: 10px;"><?=Html::encode($ticket->st_record_locator)?></td>
                <td style="border: 1px solid; padding: 10px;"><?=Html::encode($ticket->st_original_fop)?></td>
                <td style="border: 1px solid; padding: 10px;"><?=Html::encode($ticket->st_charge_system)?></td>
                <td style="border: 1px solid; padding: 10px;"><?=Html::encode($ticket->st_penalty_type)?></td>
                <td style="border: 1px solid; padding: 10px;">$<?=Html::encode($ticket->st_penalty_amount)?></td>
                <td style="border: 1px solid; padding: 10px;">$<?=Html::encode($ticket->st_selling)?></td>
                <td style="border: 1px solid; padding: 10px;">$<?=Html::encode($ticket->st_service_fee)?></td>
                <td style="border: 1px solid; padding: 10px;">$<?=Html::encode($ticket->st_recall_commission)?></td>
                <td style="border: 1px solid; padding: 10px;">$<?=Html::encode($ticket->st_markup) ?></td>
                <td style="border: 1px solid; padding: 10px;">$<?=Html::encode($ticket->st_upfront_charge)?></td>
                <td style="border: 1px solid; padding: 10px;">$<?=Html::encode($ticket->st_refundable_amount)?></td>
            </tr>
        <?php
            $totalUpfrontCharge += $ticket->st_upfront_charge;
            $totalRefundableAmount += $ticket->st_refundable_amount;
            endforeach;
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td style="border: 1px solid; padding: 10px;font-weight: bold;">Total</td>
            <td style="border: 1px solid; padding: 10px;"></td>
            <td style="border: 1px solid; padding: 10px;"></td>
            <td style="border: 1px solid; padding: 10px;"></td>
            <td style="border: 1px solid; padding: 10px;"></td>
            <td style="border: 1px solid; padding: 10px;"></td>
            <td style="border: 1px solid; padding: 10px;"></td>
            <td style="border: 1px solid; padding: 10px;"></td>
            <td style="border: 1px solid; padding: 10px;"></td>
            <td style="border: 1px solid; padding: 10px;"></td>
            <td style="border: 1px solid; padding: 10px;"></td>
            <td style="border: 1px solid; padding: 10px;font-weight: bold;"><span style="color: red;">$<?= $totalUpfrontCharge ?></span></td>
            <td style="border: 1px solid; padding: 10px;font-weight: bold;"><span style="color: red;">$<?= $totalRefundableAmount ?></span></td>
        </tr>
    </tfoot>
</table>
