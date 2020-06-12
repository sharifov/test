<?php


namespace sales\helpers\setting;


class SettingHelper
{
	public static function getCaseSaleTicketEmailData(): array
	{
		return \Yii::$app->params['settings']['case_sale_ticket_email_data'] ?? [];
	}

	public static function getCaseSaleTicketMainEmailList(): array
	{
		return self::getCaseSaleTicketEmailData()['sendTo'] ?? [];
	}
}