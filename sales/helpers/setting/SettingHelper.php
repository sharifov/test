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

    public static function quoteSearchProcessingFee(): float
    {
        return (float) (Yii::$app->params['settings']['quote_search_processing_fee'] ?? 25.00);
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

    public static function isCallRecordingSecurityEnabled(): bool
    {
        return (bool) (Yii::$app->params['settings']['call_recording_security'] ?? false);
    }

    public static function isCallRecordingLogEnabled(): bool
    {
        return (bool) (Yii::$app->params['settings']['enable_call_recording_log'] ?? false);
    }

    public static function getCallRecordingLogAdditionalCacheTimeout(): int
    {
        return (int) (Yii::$app->params['settings']['call_recording_log_additional_cache_timeout'] ?? 60);
    }

    public static function isGeneralLinePriorityEnable(): bool
    {
        return (bool)(\Yii::$app->params['settings']['enable_general_line_priority'] ?? false);
    }

    public static function getFlightQuoteAutoSelectCount(): int
    {
        return (int)(Yii::$app->params['settings']['flight_quote_auto_select_count'] ?? 3);
    }

    public static function clientDataPrivacyEnable(): bool
    {
        return (bool)(\Yii::$app->params['settings']['client_data_privacy_enabled'] ?? false);
    }

    public static function orderAutoProcessingEnable(): bool
    {
        return (bool)(\Yii::$app->params['settings']['order_auto_processing_enable'] ?? false);
    }

    public static function isAllowToUseGeneralLinePhones(): bool
    {
        return (bool)(\Yii::$app->params['settings']['allow_to_use_general_line_phones'] ?? false);
    }

    public static function isEnabledClientChatJob(): bool
    {
        return (bool)(Yii::$app->params['settings']['enable_client_chat_job'] ?? false);
    }

    public static function isCreateCaseOnOrderCancelEnabled(): bool
    {
        return (bool)(Yii::$app->params['settings']['order_cancellation_case_enabled'] ?? false);
    }

    public static function getCaseCategoryKeyOnOrderCancel(): string
    {
        return Yii::$app->params['settings']['order_cancellation_case_category_key'] ?? '';
    }

    public static function getOrderFreeCancelEmailTemplateKey(): string
    {
        return Yii::$app->params['settings']['order_free_cancel_email_template_key'] ?? 'order_free_cancel_success';
    }
}
