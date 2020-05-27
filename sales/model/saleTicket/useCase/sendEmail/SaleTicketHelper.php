<?php
namespace sales\model\saleTicket\useCase\sendEmail;


use sales\model\saleTicket\entity\SaleTicket;

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
}