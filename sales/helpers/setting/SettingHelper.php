<?php

namespace sales\helpers\setting;

use Yii;

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

    public static function isClientChatSoftCloseEnabled(): bool
    {
        return (bool)(\Yii::$app->params['settings']['client_chat_soft_close_enabled'] ?? true);
    }

    public static function getClientChatSoftCloseTimeoutHours(): int
    {
        return (int)(\Yii::$app->params['settings']['client_chat_soft_close_timeout_hours'] ?? 1);
    }

    public static function isSentryFrontendEnabled()
    {
        return (bool)(\Yii::$app->params['settings']['sentry_frontend_enabled'] ?? false);
    }

    public static function processingFee(): float
    {
        return (float) (\Yii::$app->params['settings']['processing_fee'] ?? 25.00);
    }

    public static function userSiteActivityLogHistoryDays(): int
    {
        return (int) (Yii::$app->params['settings']['user_site_activity_log_history_days'] ?? 3);
    }

    public static function consoleLogCleanerEnable(): bool
    {
        return (bool) (Yii::$app->params['settings']['console_log_cleaner_enable'] ?? false);
    }

    public static function consoleLogCleanerParamsDays(): int
    {
        return (int) (Yii::$app->params['settings']['console_log_cleaner_params']['days'] ?? 90);
    }

    public static function metricsEnabled(): bool
    {
        return (bool) (Yii::$app->params['settings']['metrics_enabled'] ?? false);
    }

    public static function cleanUserMonitorAfterDays(): int
    {
        return (int) (Yii::$app->params['settings']['clean_user_monitor_after_days'] ?? 7);
    }

    public static function cleanCallAfterDays(): int
    {
        return (int) (Yii::$app->params['settings']['clean_call_after_days'] ?? 10);
    }
}
