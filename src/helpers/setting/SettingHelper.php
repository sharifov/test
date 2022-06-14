<?php

namespace src\helpers\setting;

use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Lead;
use frontend\helpers\JsonHelper;
use modules\shiftSchedule\src\services\ShiftScheduleDictionary;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class SettingHelper
{
    private static ?array $callSpamFilter = null;
    private static ?array $callbackToCaller = null;

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

    public static function getMetricJobTimeWaiting(): int
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

    public static function getLeadTravelDatesPassedTrashedHours(): int
    {
        return (int)(Yii::$app->params['settings']['leads_travel_dates_passed_trashed_hours'] ?? 24);
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
            'priority_level' => $sort['DESC'],
            'gross_profit' => $sort['DESC'],
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

        if (empty($finalSort['priority_level'])) {
            unset($finalSort['priority_level']);
        }

        if (empty($finalSort['gross_profit'])) {
            unset($finalSort['gross_profit']);
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
        return (int) (Yii::$app->params['settings']['redial_user_access_expired_seconds'] ?? 20);
    }

    public static function getCallSpamFilterData(): array
    {
        $settings = Yii::$app->params['settings']['call_spam_filter'] ?? null;
        if (self::$callSpamFilter !== null) {
            return self::$callSpamFilter;
        }
        if ($settings) {
            self::$callSpamFilter = JsonHelper::decode($settings);
        }
        return self::$callSpamFilter ?? [
                'enabled' => false,
                'spam_rate' => 0.75,
                'redialEnabled' => false,
                'message' => '',
                'trust_rate' => 0.8
        ];
    }

    public static function callSpamFilterEnabled(): bool
    {
        return (bool) (self::getCallSpamFilterData()['enabled'] ?? false);
    }

    public static function getCallSpamFilterRate(): float
    {
        return (float) (self::getCallSpamFilterData()['spam_rate'] ?? 0.3567);
    }

    public static function getCallTrustFilterRate(): float
    {
        return (float) (self::getCallSpamFilterData()['trust_rate'] ?? 0.8);
    }

    public static function getCallSpamFilterMessage(): string
    {
        return (string) (self::getCallSpamFilterData()['message'] ?? '');
    }

    public static function getCallbackToCallerData()
    {
        $settings = Yii::$app->params['settings']['callback_to_caller'] ?? null;
        if (self::$callbackToCaller !== null) {
            return self::$callbackToCaller;
        }
        if ($settings) {
            self::$callbackToCaller = JsonHelper::decode($settings);
        }
        return self::$callbackToCaller ?? [
            'enabled' => false,
            'message' => '',
            'curlTimeout' => 30,
            'dialCallTimeout' => 10,
            'dialCallLimit' => 1,
            'successStatusList' => [
                'busy'
            ]
        ];
    }

    public static function isCallbackToCallerEnabled(): bool
    {
        return (bool) (self::getCallbackToCallerData()['enabled'] ?? false);
    }

    public static function getCallbackToCallerMessage(): string
    {
        return (string) (self::getCallbackToCallerData()['message'] ?? '');
    }

    public static function getCallbackToCallerCurlTimeout(): int
    {
        return (int) (self::getCallbackToCallerData()['curlTimeout'] ?? 30);
    }

    public static function getCallbackToCallerDialCallTimeout(): int
    {
        return (int) (self::getCallbackToCallerData()['dialCallTimeout'] ?? 10);
    }

    public static function getCallbackToCallerDialCallLimit(): int
    {
        return (int) (self::getCallbackToCallerData()['dialCallLimit'] ?? 1);
    }

    public static function getCallbackToCallerSuccessStatusList(): array
    {
        return (self::getCallbackToCallerData()['successStatusList'] ?? [
            'busy'
        ]);
    }

    public static function getCallbackToCallerExcludedProjectList(): array
    {
        return self::getCallbackToCallerData()['excludeProjectKeys'] ?? [
            'priceline'
        ];
    }

    public static function getCallbackToCallerExcludedDepartmentList(): array
    {
        return self::getCallbackToCallerData()['excludeDepartmentKeys'] ?? [];
    }

    public static function getCalculateGrossProfitInDays(): int
    {
        return (int) (Yii::$app->params['settings']['calculate_gross_profit_in_days'] ?? 14);
    }

    public static function getCalculatePriorityLevelInDays(): int
    {
        return (int) (Yii::$app->params['settings']['calculate_priority_level_in_days'] ?? 14);
    }

    public static function clientNotificationSuccessCallMinDuration(): int
    {
        return (int) (Yii::$app->params['settings']['client_notification_success_call_min_duration'] ?? 30);
    }

    public static function getVoluntaryExchangeCaseCategory(): ?string
    {
        return Yii::$app->params['settings']['voluntary_exchange_case_category'] ?? null;
    }

    private static function prePareStatusIds($setting): array
    {
        if (($statuses = $setting ?? null) && is_array($statuses)) {
            return array_keys($statuses);
        }
        return [];
    }

    public static function getProductQuoteChangeableStatuses(): array
    {
        return self::prePareStatusIds(Yii::$app->params['settings']['product_quote_changeable_statuses']);
    }

    public static function getActiveQuoteChangeStatuses(): array
    {
        return self::prePareStatusIds(Yii::$app->params['settings']['active_quote_change_statuses']);
    }

    public static function getAcceptedQuoteChangeStatuses(): array
    {
        return self::prePareStatusIds(Yii::$app->params['settings']['accepted_quote_change_statuses']);
    }

    public static function getActiveQuoteRefundStatuses(): array
    {
        return self::prePareStatusIds(Yii::$app->params['settings']['active_quote_refund_statuses']);
    }

    public static function getAcceptedQuoteRefundStatuses(): array
    {
        return self::prePareStatusIds(Yii::$app->params['settings']['accepted_quote_refund_statuses']);
    }

    public static function getFinishedQuoteChangeStatuses(): array
    {
        return self::prePareStatusIds(Yii::$app->params['settings']['finished_quote_change_statuses']);
    }

    public static function getFinishedQuoteRefundStatuses(): array
    {
        return self::prePareStatusIds(Yii::$app->params['settings']['finished_quote_refund_statuses']);
    }

    public static function getExchangeQuoteConfirmStatusList(): array
    {
        return self::prePareStatusIds(Yii::$app->params['settings']['exchange_quote_confirm_status_list']);
    }

    public static function getUpdatableInvoluntaryQuoteChange(): array
    {
        return self::prePareStatusIds(Yii::$app->params['settings']['updatable_involuntary_quote_change']);
    }

    public static function getInvoluntaryChangeActiveStatuses(): array
    {
        return self::prePareStatusIds(Yii::$app->params['settings']['involuntary_change_active_statuses']);
    }

    public static function getVoluntaryExchangeBoEndpoint(): ?string
    {
        return Yii::$app->params['settings']['voluntary_exchange_bo_endpoint'] ?? null;
    }

    public static function getVoluntaryRefundBoEndpoint(): ?string
    {
        return Yii::$app->params['settings']['voluntary_refund_bo_endpoint'] ?? null;
    }

    public static function getVoluntaryRefundCaseCategory(): ?string
    {
        return Yii::$app->params['settings']['voluntary_refund_case_category'] ?? null;
    }

    public static function getProductQuoteChangeClientStatusMapping(): array
    {
        return Yii::$app->params['settings']['product_quote_change_client_status_mapping'] ?? [];
    }

    public static function getProductQuoteRefundClientStatusMapping(): array
    {
        return Yii::$app->params['settings']['product_quote_refund_client_status_mapping'] ?? [];
    }

    public static function getRedialCheckIsOnCallTime(): int
    {
        return Yii::$app->params['settings']['lead_redial_is_on_call_check_time'] ?? 20;
    }

    public static function isClientChatLeadAutoTakeOnChatAccept(): bool
    {
        return (bool)(\Yii::$app->params['settings']['client_chat_lead_auto_take']['on_chat_accept'] ?? false);
    }

    public static function phoneDeviceLogsEnabled(): bool
    {
        return (bool)(\Yii::$app->params['settings']['phone_device_logs_enabled'] ?? false);
    }

    public static function leadRedialQCallAttemptsFromTimeValidationEnabled(): bool
    {
        return (bool) (Yii::$app->params['settings']['lead_redial_qcall_attempts_from_time_validation_enabled'] ?? true);
    }

    public static function getDbCryptBlockEncryptionMode(): ?string
    {
        return \Yii::$app->params['settings']['db_crypt_block_encryption_mode'] ?? null;
    }

    public static function getDbCryptKeyStr(): ?string
    {
        return \Yii::$app->params['settings']['db_crypt_key_str'] ?? null;
    }

    public static function getDbCryptInitVector(): ?string
    {
        return \Yii::$app->params['settings']['db_crypt_init_vector'] ?? null;
    }

    public static function getUserPrickedCallDuration(): int
    {
        return (int)(\Yii::$app->params['settings']['user_picked_call_duration'] ?? 30);
    }

    public static function leadRedialEnabled(): bool
    {
        return (bool) (Yii::$app->params['settings']['lead_redial_enabled'] ?? false);
    }

    public static function getCallReconnectAnnounceMessage(): string
    {
        return (string) (Yii::$app->params['settings']['call_reconnect_announce'] ?? 'Connection Error. Reconnecting. Please hold');
    }

    public static function isTwoFactorAuthEnabled(): bool
    {
        return (bool) (Yii::$app->params['settings']['two_factor_authentication_enable'] ?? false);
    }

    public static function getTwoFactorAuthCounter(): int
    {
        return (int) (Yii::$app->params['settings']['two_factor_counter'] ?? 60);
    }

    public static function isEnabledAuthClients(): bool
    {
        return (self::isEnabledGoogleAuthClient() || self::isEnabledMicrosoftAuthClient());
    }

    public static function isEnabledGoogleAuthClient(): bool
    {
        return (bool) (Yii::$app->params['settings']['enable_auth_clients']['auth_google'] ?? false);
    }

    public static function isEnabledMicrosoftAuthClient(): bool
    {
        return (bool) (Yii::$app->params['settings']['enable_auth_clients']['auth_microsoft'] ?? false);
    }

    public static function getCleanLeadPoorProcessingLogAfterDays(): int
    {
        return (int)(Yii::$app->params['settings']['clean_lead_poor_processing_log_after_days'] ?? 90);
    }

    public static function getSnoozeLimit(): int
    {
        return (int)(Yii::$app->params['settings']['snooze_limit'] ?? 10);
    }

    public static function getSmsTemplateForRemovingLpp(): array
    {
        return Yii::$app->params['settings']['lpp_remove_by_sms_tpl'] ?? [];
    }

    public static function isPhoneNumberRedialEnabled(): bool
    {
        return (bool)(Yii::$app->params['settings']['phone_number_redial_enable'] ?? false);
    }

    public static function getRedialLeadExcludeAttributes(): array
    {
        $settings = Yii::$app->params['settings']['redial_lead_exclude_attributes'] ?? [];
        if (!is_array($settings)) {
            Yii::error([
                'message' => 'Redial lead exclude attributes settings is invalid. Value must be array',
                'settingsKey' => 'redial_lead_exclude_attributes',
            ], 'SettingsHelper:getRedialLeadExcludeAttributes');
            return [];
        }

        if (array_key_exists('projects', $settings) && is_array($settings['projects'])) {
            $projects = $settings['projects'];
        } else {
            self::leadRedialExcludeAttributesErrorLog('projects');
            return [];
        }

        if (array_key_exists('departments', $settings) && is_array($settings['departments'])) {
            $departments = $settings['departments'];
        } else {
            self::leadRedialExcludeAttributesErrorLog('departments');
            return [];
        }

        if (array_key_exists('cabins', $settings) && is_array($settings['cabins'])) {
            $cabins = $settings['cabins'];
        } else {
            self::leadRedialExcludeAttributesErrorLog('cabins');
            return [];
        }

        if (array_key_exists('noFlightDetails', $settings)) {
            $noFlightDetails = (bool)$settings['noFlightDetails'];
        } else {
            self::leadRedialExcludeAttributesErrorLog('noFlightDetails');
            return [];
        }

        if (array_key_exists('isTest', $settings)) {
            $isTest = (bool)$settings['isTest'];
        } else {
            self::leadRedialExcludeAttributesErrorLog('isTest');
            return [];
        }

        if (array_key_exists('sources', $settings) && is_array($settings['sources'])) {
            $sources = $settings['sources'];
        } else {
            self::leadRedialExcludeAttributesErrorLog('sources');
            return [];
        }

        return [
            'projects' => $projects,
            'departments' => $departments,
            'sources' => $sources,
            'cabins' => $cabins,
            'noFlightDetails' => $noFlightDetails,
            'isTest' => $isTest
        ];
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    public static function getPriceResearchLinksNamesArray(): array
    {
        $researchLinks = Yii::$app->params['settings']['price_research_links'] ?? null;
        if (empty($researchLinks)) {
            $error = 'Price research links settings mot found. Value must be array';
            throw new \RuntimeException($error);
        }
        if (!is_array($researchLinks)) {
            $error = 'Price research links settings is invalid. Value must be array';
            throw new \RuntimeException($error);
        }
        $results = [];
        foreach ($researchLinks as $key => $researchLink) {
            if (!is_array($researchLink)) {
                $error = 'Price research links settings is invalid. Link value must be array, arrayKey is ' . $researchLink;
                throw new \RuntimeException($error);
            }
            if (!ArrayHelper::getValue($researchLink, 'enabled')) {
                continue;
            }
            $linkName  = ArrayHelper::getValue($researchLink, 'name');
            $results[$key] = $linkName;
        }
        return $results;
    }

    /**
     * @param int $key
     * @return array
     * @throws \RuntimeException
     */
    public static function getPriceResearchLinkByKey(string $key): array
    {
        $researchLinks = Yii::$app->params['settings']['price_research_links'] ?? null;
        if (empty($researchLinks)) {
            $error = 'Price research links settings mot found. Value must be array';
            throw new NotFoundHttpException($error);
        }
        if (!is_array($researchLinks)) {
            $error = 'Price research links settings is invalid. Value must be array';
            throw  new  NotFoundHttpException($error);
        }
        if (!array_key_exists($key, $researchLinks)) {
            $error = 'Price research links settings key is invalid. Value not found arrayKey is' . $key;
            throw new  NotFoundHttpException($error);
        }
        $element = $researchLinks[$key];
        if (!is_array($element)) {
            $error = 'Price research links settings is invalid. Link value must be array arrayKey is' . $key;
            throw new  NotFoundHttpException($error);
        }

        return $element;
    }


    private static function leadRedialExcludeAttributesErrorLog(string $key): void
    {
        \Yii::error([
            'message' => 'One setting in Lead Redial Exclude Attributes is missing or is invalid',
            'settingsKey' => 'redial_lead_exclude_attributes',
            'key' => $key,
            'forAdmin' => true,
        ], 'SettingsHelper:leadRedialExcludeAttributesErrorLog');
    }

    private static function getShiftSchedule(): array
    {
        return Yii::$app->params['settings']['shift_schedule'] ?? [
                'generate_enabled' => ShiftScheduleDictionary::DEFAULT_GENERATE_ENABLED,
                'days_limit' => ShiftScheduleDictionary::DEFAULT_DAYS_LIMIT,
                'days_offset' => ShiftScheduleDictionary::DEFAULT_DAYS_OFFSET
            ];
    }

    public static function getShiftScheduleGenerateEnabled(): bool
    {
        return (bool)(self::getShiftSchedule()['generate_enabled'] ?? ShiftScheduleDictionary::DEFAULT_GENERATE_ENABLED);
    }

    public static function getShiftScheduleDaysLimit(): int
    {
        $daysLimit = (int)(self::getShiftSchedule()['days_limit'] ?? 0);

        if ($daysLimit <= 0) {
            Yii::warning([
                'message' => 'Days limit cannot be less or equal to 0',
                'daysLimit' => $daysLimit,
            ], 'SettingHelper:getShiftScheduleDaysLimit:DaysLimitIsInvalid');

            return ShiftScheduleDictionary::DEFAULT_DAYS_LIMIT;
        }
        return $daysLimit;
    }

    public static function getShiftScheduleDaysOffset(): int
    {
        $daysOffset = (int) (self::getShiftSchedule()['days_offset'] ?? ShiftScheduleDictionary::DEFAULT_DAYS_OFFSET);
        if ($daysOffset < 0) {
            Yii::warning([
                'message' => 'DaysOffset cannot be less to 0',
                'daysOffset' => $daysOffset,
            ], 'SettingHelper:getShiftScheduleDaysOffset:DaysOffsetLimitIsInvalid');
            return ShiftScheduleDictionary::DEFAULT_DAYS_OFFSET;
        }
        return $daysOffset;
    }

    public static function isClientChatDebugEnable(): bool
    {
        return (bool) (Yii::$app->params['settings']['client_chat_debug_enable'] ?? false);
    }

    public static function isEnableAgentCallQueueJobAfterChangeCallStatusReady(): bool
    {
        return (bool) (Yii::$app->params['settings']['enable_agent_call_queue_job_after_change_call_status_ready'] ?? true);
    }
}
