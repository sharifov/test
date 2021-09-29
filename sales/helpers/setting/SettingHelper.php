<?php

namespace sales\helpers\setting;

use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Lead;
use frontend\helpers\JsonHelper;
use sales\helpers\app\AppHelper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

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
        return (float)(\Yii::$app->params['settings']['processing_fee'] ?? 25.00);
    }

    public static function quoteSearchProcessingFee(): float
    {
        return (float)(Yii::$app->params['settings']['quote_search_processing_fee'] ?? 25.00);
    }

    public static function userSiteActivityLogHistoryDays(): int
    {
        return (int)(Yii::$app->params['settings']['user_site_activity_log_history_days'] ?? 3);
    }

    public static function consoleLogCleanerEnable(): bool
    {
        return (bool)(Yii::$app->params['settings']['console_log_cleaner_enable'] ?? false);
    }

    public static function consoleLogCleanerParamsDays(): int
    {
        return (int)(Yii::$app->params['settings']['console_log_cleaner_params']['days'] ?? 90);
    }

    public static function metricsEnabled(): bool
    {
        return (bool)(Yii::$app->params['settings']['metrics_enabled'] ?? false);
    }

    public static function cleanUserMonitorAfterDays(): int
    {
        return (int)(Yii::$app->params['settings']['clean_user_monitor_after_days'] ?? 7);
    }

    public static function cleanCallAfterDays(): int
    {
        return (int)(Yii::$app->params['settings']['clean_call_after_days'] ?? 10);
    }

    public static function isCallRecordingSecurityEnabled(): bool
    {
        return (bool)(Yii::$app->params['settings']['call_recording_security'] ?? false);
    }

    public static function isCallRecordingLogEnabled(): bool
    {
        return (bool)(Yii::$app->params['settings']['enable_call_recording_log'] ?? false);
    }

    public static function getCallRecordingLogAdditionalCacheTimeout(): int
    {
        return (int)(Yii::$app->params['settings']['call_recording_log_additional_cache_timeout'] ?? 60);
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

    public static function getOrderProcessingEmailTemplateKey(): string
    {
        return Yii::$app->params['settings']['order_processing_email_template_key'] ?? 'order_status';
    }

    public static function getOrderCompleteEmailTemplateKey(): string
    {
        return Yii::$app->params['settings']['order_complete_email_template_key'] ?? 'order_status';
    }

    public static function clientChatUserAccessHistoryDays(): int
    {
        return (int)(Yii::$app->params['settings']['client_chat_user_access_history_days'] ?? 5);
    }

    public static function getTimeStartCallUserAccessGeneral(?Department $department, $phone): int
    {
        $key = 'time_start_call_user_access_general';
        if ($phone) {
            $result = self::getDepartmentPhoneQueueDistributionParam($phone, $key);
            if ($result !== null) {
                return $result;
            }
        }
        if ($department) {
            $params = $department->getParams();
            if ($params && $params->queueDistribution->timeStartCallUserAccessGeneral !== null) {
                return (int)$params->queueDistribution->timeStartCallUserAccessGeneral;
            }
        }
        return (int)(Yii::$app->params['settings'][$key] ?? 0);
    }

    public static function getGeneralLineUserLimit(?Department $department, $phone): int
    {
        $key = 'general_line_user_limit';
        if ($phone) {
            $result = self::getDepartmentPhoneQueueDistributionParam($phone, $key);
            if ($result !== null) {
                return $result;
            }
        }
        if ($department) {
            $params = $department->getParams();
            if ($params && $params->queueDistribution->generalLineUserLimit !== null) {
                return (int)$params->queueDistribution->generalLineUserLimit;
            }
        }
        return (int)(Yii::$app->params['settings'][$key] ?? 1);
    }

    public static function getTimeRepeatCallUserAccess(?Department $department, $phone): int
    {
        $key = 'time_repeat_call_user_access';
        if ($phone) {
            $result = self::getDepartmentPhoneQueueDistributionParam($phone, $key);
            if ($result !== null) {
                return $result;
            }
        }
        if ($department) {
            $params = $department->getParams();
            if ($params && $params->queueDistribution->timeRepeatCallUserAccess !== null) {
                return (int)$params->queueDistribution->timeRepeatCallUserAccess;
            }
        }
        return (int)(Yii::$app->params['settings'][$key] ?? 0);
    }

    private static function getDepartmentPhoneQueueDistributionParam(string $phone, string $key): ?int
    {
        $params = DepartmentPhoneProject::find()->select(['dpp_params'])->byPhone($phone, false)->scalar();
        if ($params) {
            $params = @json_decode($params, true);
            if ($params) {
                $params = @json_decode($params, true);
                if ($params && isset($params['queue_distribution'][$key])) {
                    return (int)$params['queue_distribution'][$key];
                }
            }
        }
        return null;
    }

    public static function isWebhookOrderUpdateBOEnabled()
    {
        return Yii::$app->params['settings']['webhook_order_update_bo_enabled'] ?? true;
    }

    public static function isWebhookOrderUpdateHybridEnabled()
    {
        return Yii::$app->params['settings']['webhook_order_update_hybrid_enabled'] ?? true;
    }

    public static function getWebhookOrderUpdateBOEndpoint(): ?string
    {
        return Yii::$app->params['settings']['webhook_order_update_bo_endpoint'] ?? null;
    }

    public static function getWebhookOrderUpdateHybridEndpoint()
    {
        return Yii::$app->params['settings']['webhook_order_update_hybrid_endpoint'] ?? null;
    }

    public static function warmTransferTimeout(): int
    {
        return (int)(\Yii::$app->params['settings']['warm_transfer_timeout'] ?? 30);
    }

    public static function warmTransferAutoUnholdEnabled(): bool
    {
        return (bool)(\Yii::$app->params['settings']['warm_transfer_auto_unhold_enabled'] ?? false);
    }

    public static function isPhoneBlacklistEnabled(): bool
    {
        return (bool)(Yii::$app->params['settings']['phone_blacklist_enabled'] ?? true);
    }

    public static function getPhoneBlacklistLastTimePeriod(): int
    {
        return (int)(Yii::$app->params['settings']['phone_blacklist_last_time_period'] ?? 1440);
    }

    public static function getPhoneBlacklistPeriodList(): array
    {
        $list = Yii::$app->params['settings']['phone_blacklist_period'] ?? [];
        if ($list) {
            return JsonHelper::decode($list);
        }
        return [
            1 => 5,
            2 => 10,
            3 => 60
        ];
    }

    public static function getPhoneBlacklistPeriodByIndex(int $index): int
    {
        $period = self::getPhoneBlacklistPeriodList();
        foreach ($period as $count => $minutes) {
            if ($index <= $count) {
                return $minutes;
            }
        }
        return $period[array_key_last($period)];
    }

    public static function getLeadApiGoogleAllowCreate(): int
    {
        return (bool)ArrayHelper::getValue(Yii::$app->params['settings'], 'lead_api_google.allow_create', false);
    }

    public static function getLeadApiGoogleStatusId(): int
    {
        return (int)ArrayHelper::getValue(Yii::$app->params['settings'], 'lead_api_google.default_status_id', Lead::STATUS_PENDING);
    }

    public static function getLeadApiGoogleDepartmentId(): int
    {
        return (int)ArrayHelper::getValue(Yii::$app->params['settings'], 'lead_api_google.default_department_id', Department::DEPARTMENT_SALES);
    }

    public static function getMetricJobTimeExecution(): int
    {
        return (int)ArrayHelper::getValue(Yii::$app->params['settings'], 'metric_job_time_execution', 60);
    }

    public static function getFrontendWidgetList(): array
    {
        return ArrayHelper::getValue(Yii::$app->params['settings'], 'frontend_widget_list');
    }

    public static function getFrontendWidgetByKey(string $key): array
    {
        return ArrayHelper::getValue(Yii::$app->params['settings'], 'frontend_widget_list.' . $key, []);
    }

    public static function getCasePastDepartureDate(): int
    {
        return (int)(Yii::$app->params['settings']['case_past_departure_date'] ?? 2);
    }

    public static function getCasePriorityDays(): int
    {
        return (int)(Yii::$app->params['settings']['case_priority_days'] ?? 14);
    }

    public static function isEnableCheckPhoneByNeutrino(): bool
    {
        return (bool)(Yii::$app->params['settings']['enable_check_phone_by_neutrino'] ?? false);
    }

    public static function getCallTerminateBlackList(): ?array
    {
        return ArrayHelper::getValue(Yii::$app->params['settings'], 'call_terminate_black_list');
    }

    public static function getCallTerminateBlackListByKey(string $key)
    {
        return ArrayHelper::getValue(Yii::$app->params['settings'], 'call_terminate_black_list.' . $key);
    }

    public static function getCallDurationSecondsGlCount(): int
    {
        return (int)(Yii::$app->params['settings']['call_duration_seconds_gl_count'] ?? 20);
    }

    public static function isEnableOrderFromSale(): bool
    {
        return (bool)(Yii::$app->params['settings']['enable_order_from_sale'] ?? false);
    }

    public static function getNotificationsHistoryDays(): int
    {
        return (int)(Yii::$app->params['settings']['notifications_history_days'] ?? 30);
    }

    public static function getClientNotificationsHistoryDays(): int
    {
        return (int)(Yii::$app->params['settings']['client_notifications_history_days'] ?? 30);
    }

    public static function getCallDistributionSort(): array
    {
        $sort = [
            'ASC' => SORT_ASC,
            'DESC' => SORT_DESC
        ];

        $defaultSort = [
            'general_line_call_count' => null,
            'phone_ready_time' => $sort['ASC'],
        ];

        $callDistributionSort = Yii::$app->params['settings']['call_distribution_sort'] ?? [
                'phone_ready_time' => $sort['ASC']
            ];

        $finalSort = [];

        foreach ($callDistributionSort as $key => $item) {
            $item = mb_strtoupper($item);

            if (!empty($callDistributionSort[$key]) && array_key_exists($key, $defaultSort) && array_key_exists($item, $sort)) {
                $finalSort[$key] = $sort[$item];
            }
        }

        if (empty($finalSort['general_line_call_count'])) {
            unset($finalSort['general_line_call_count']);
        }

        return $finalSort;
    }

    public static function getLimitUserConnection(): int
    {
        return (int)(Yii::$app->params['settings']['limit_user_connection'] ?? 10);
    }


    public static function getReProtectionCaseCategory(): ?string
    {
        return Yii::$app->params['settings']['reprotection_case_category'] ?? null;
    }

    public static function getSchdCaseDeadlineHours(): int
    {
        return (int)(Yii::$app->params['settings']['schd_case_deadline_hours'] ?? 0);
    }

    public static function getYandexMetrika(): array
    {
        return Yii::$app->params['settings']['yandex_metrika'] ?? [];
    }

    public static function isEnableSendHookToOtaReProtectionCreate(): bool
    {
        return (bool)(Yii::$app->params['settings']['enable_send_hook_to_ota_re_protection_create'] ?? true);
    }

    public static function isClientChatApiLogEnabled(): bool
    {
        return (bool)(Yii::$app->params['settings']['client_chat_api_log_enabled'] ?? true);
    }

    /**
     * @return int
     */
    public static function getLeadAutoRedialDelay(): int
    {
        return (int)(Yii::$app->params['settings']['call_lead_auto_redial_delay'] ?? 0);
    }

    /**
     * @return bool
     */
    public static function getLeadAutoRedialEnabled(): bool
    {
        return (bool)(Yii::$app->params['settings']['call_lead_auto_redial_enabled'] ?? false);
    }

    /**
     * @return array [days, hours]
     */
    public static function getClientNotificationStartInterval(): array
    {
        $settings = Yii::$app->params['settings']['client_notification_start_interval'] ?? null;
        if ($settings) {
            return JsonHelper::decode($settings);
        }
        return ['days' => 0, 'hours' => 0];
    }

    /**
     * @return int
     */
    public static function getTrashActiveDaysLimitGlobal(): int
    {
        return (int)(Yii::$app->params['settings']['trash_cases_active_days_limit'] ?? 0);
    }

    public static function getRedialAutoTakeSeconds(): int
    {
        return Yii::$app->params['settings']['redial_auto_take_seconds'] ?? 10;
    }

    public static function isEnableCallLogFilterGuard(): bool
    {
        return (bool) (Yii::$app->params['settings']['is_call_log_filter_guard'] ?? false);
    }

    public static function getLimitLeadsInContactInfoInPhoneWidget(): int
    {
        return (int) (Yii::$app->params['settings']['limit_leads_in_phone_widget'] ?? 3);
    }


    public static function getLeadRedialAccessExpiredSeconds(): int
    {
        return 20; //todo
    }

    public static function getRedialGetLimitAgents(): int
    {
        return (int) (Yii::$app->params['settings']['redial_get_limit_agents'] ?? 5);
    }

    public static function getBusinessProjectIds(): array
    {
        $settings = Yii::$app->params['settings']['business_project_ids'] ?? null;
        if ($settings) {
            return JsonHelper::decode($settings);
        }
        return [7];
    }

    public static function getRedialBusinessFlightLeadsMinimumSkillLevel(): int
    {
        return (int) (Yii::$app->params['settings']['redial_business_flight_leads_minimum_skill_level'] ?? 0);
    }

    public static function getRedialUserAccessExpiredSecondsLimit(): int
    {
        return (int) (Yii::$app->params['settings']['redial_user_access_expired_seconds'] ?? 60);
    }
}
