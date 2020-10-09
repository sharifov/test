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

	public static function isClientChatRealTimeMonitoringEnabled()
    {
        return \Yii::$app->params['settings']['client_chat_real_time_monitoring'] ?? false;
    }

    public static function isOriginalPhoneWidgetEnabled(): bool
	{
		return \Yii::$app->params['settings']['enable_original_phone_widget'] ?? true;
	}

	public static function getRcNameForRegisterChannelInRc(): string
	{
		return \Yii::$app->params['settings']['rc_username_for_register_channel'] ?? '';
	}

	public static function getChatWidgetLimitRequests(): int
	{
		return (int)(\Yii::$app->params['settings']['chat_widget_limit_requests'] ?? 20);
	}
}