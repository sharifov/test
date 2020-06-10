<?php

use common\models\CaseSale;
use common\models\CreditCard;
use common\models\Employee;
use sales\helpers\cases\CaseSaleHelper;
use sales\model\saleTicket\entity\SaleTicket;
use yii\helpers\Html;


/** @var $saleTickets SaleTicket[] */
/** @var $caseSale CaseSale */
/** @var $creditCards CreditCard[] */
/** @var $user Employee */

$chargeSystem = null;
$transactionIds = null;
$fop = null;
$isNeedAdditionalInfoForEmail = false;
$saleData = $caseSale ? json_decode((string)$caseSale->css_sale_data, true) : [];
$groups = $user->getUserGroupList();
?>
<ul style="padding: 0;list-style: none;">
    <li><b>Report by:</b> <?= $user->username ?></li>
    <li><b>Reporter group:</b> <?= array_shift($groups) ?></li>
</ul>
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
            <th style="border: 1px solid; padding: 10px;">Original Profit</th>
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
                <td style="border: 1px solid; padding: 10px;"><?=Html::encode($ticket->getFormattedOriginalFop())?></td>
                <td style="border: 1px solid; padding: 10px;"><?=Html::encode($ticket->st_charge_system)?></td>
                <td style="border: 1px solid; padding: 10px;"><?=Html::encode(SaleTicket::getPenaltyTypeName($ticket->st_penalty_type))?></td>
                <td style="border: 1px solid; padding: 10px;"><?=Html::encode(empty($ticket->st_refund_waiver) ? ('$'.$ticket->st_penalty_amount) : $ticket->st_refund_waiver)?></td>
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
            $chargeSystem = $ticket->st_charge_system;
            $transactionIds = $ticket->st_transaction_ids;
            $fop = $ticket->st_original_fop;

            if (!$isNeedAdditionalInfoForEmail) {
                $isNeedAdditionalInfoForEmail = $ticket->isNeedAdditionalInfoForEmail();
            }

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

<br>
<br>
<br>

<table>
    <tr>
        <td>
			<?php if($isNeedAdditionalInfoForEmail && $caseSale): ?>
                <table width="25%" cellpadding="0" cellspacing="0" style="width:25%;">
                    <tr>
                        <th style="border: 1px solid; padding: 10px;">Sale Id</th>
                        <td style="border: 1px solid; padding: 10px;"><?= $caseSale->css_sale_id ?></td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid; padding: 10px;">PNR</th>
                        <td style="border: 1px solid; padding: 10px;"><?= $caseSale->css_sale_pnr ?></td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid; padding: 10px;">Charge System</th>
                        <td style="border: 1px solid; padding: 10px;"><?= $chargeSystem ?></td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid; padding: 10px;">Trans. IDs</th>
                        <td style="border: 1px solid; padding: 10px;"><?= $transactionIds ?></td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid; padding: 10px;">Trans. Date</th>
                        <td style="border: 1px solid; padding: 10px;"><?= CaseSaleHelper::getTransactionDate($saleData) ?></td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid; padding: 10px;">Card Number</th>
                        <td style="border: 1px solid; padding: 10px;"><?= CaseSaleHelper::getCardNumbers($saleData) ?></td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid; padding: 10px;">Refundable Amount</th>
                        <td style="border: 1px solid; padding: 10px;">$<?= $totalRefundableAmount ?></td>
                    </tr>
                </table>
			<?php endif; ?>
        </td>
        <td style="vertical-align: top;">
			<?php if($creditCards): ?>
                <table width="40%" cellpadding="0" cellspacing="0" style="width:40%;">
                    <thead>
                    <tr>
                        <th style="border: 1px solid; padding: 10px;">Display Number</th>
                        <th style="border: 1px solid; padding: 10px;">Holder Name</th>
                        <th style="border: 1px solid; padding: 10px;">B/O Synchronised</th>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach($creditCards as $card): ?>
                        <tr>
                            <td style="border: 1px solid; padding: 10px;"><?= $card->cc_display_number ?></td>
                            <td style="border: 1px solid; padding: 10px;"><?= $card->cc_holder_name ?></td>
                            <td style="border: 1px solid; padding: 10px;"><?= $card->cc_is_sync_bo ? 'Yes' : '--' ?></td>
                        </tr>
					<?php endforeach; ?>
                    </tbody>
                </table>
			<?php endif; ?>
        </td>
    </tr>
</table>
