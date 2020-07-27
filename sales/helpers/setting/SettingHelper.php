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

	public static function isCaseCommunicationNewCallWidgetEnabled()
	{
		return \Yii::$app->params['settings']['case_communication_new_call_widget'] ?? false;
	}

	public static function isLeadCommunicationNewCallWidgetEnabled()
	{
		return \Yii::$app->params['settings']['lead_communication_new_call_widget'] ?? false;
	}

	public static function isClientChatEnabled(): bool
	{
		return \Yii::$app->params['settings']['enable_client_chat'] ?? false;
	}

	public static function isCcSoundNotificationEnabled(): bool
	{
		return \Yii::$app->params['settings']['cc_sound_notification_enable'] ?? false;
	}
}