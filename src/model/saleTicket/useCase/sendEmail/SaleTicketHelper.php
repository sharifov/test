<?php

namespace src\model\saleTicket\useCase\sendEmail;

use src\helpers\cases\CaseSaleHelper;
use src\model\saleTicket\entity\SaleTicket;

class SaleTicketHelper
{
    /**
     * @param SaleTicket[] $saleTickets
     * @return string
     */
    public static function getTitleForSendEmailBtn(array $saleTickets): string
    {
        $isNeedAdditionalInfoForEmail = false;
        foreach ($saleTickets as $saleTicket) {
            $isNeedAdditionalInfoForEmail = $saleTicket->isNeedAdditionalInfoForEmail();
            if ($isNeedAdditionalInfoForEmail) {
                break;
            }
        }
        $emailSettings = \Yii::$app->params['settings']['case_sale_ticket_email_data'];

        $title = 'Send Email to: ' . implode(', ', $emailSettings['sendTo'] ?? []);

        if ($isNeedAdditionalInfoForEmail) {
            $title .= '; Additionally To: ' . implode(', ', $emailSettings['bookeepingEmails'] ?? []);
        }

        return $title;
    }

    /**
     * @param array $saleTickets
     * @param array $caseSaleData
     * @return bool
     */
    public static function isRecallCommissionChanged(array $saleTickets, array $caseSaleData): bool
    {
        /** @var SaleTicket $saleTicket */
        foreach ($saleTickets as $saleTicket) {
            if ((float)$saleTicket->st_recall_commission !== CaseSaleHelper::getRecallCommission($caseSaleData)) {
                return true;
            }
        }
        return false;
    }
}
