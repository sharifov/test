<?php

namespace modules\featureFlag;

use kivork\FeatureFlag\Models\FeatureFlagObjectModelInterface;
use kivork\FeatureFlag\Models\flags\dateTime\DateTimeFeatureFlag;
use modules\featureFlag\models\debug\DebugFeatureFlag;
use modules\featureFlag\models\user\UserFeatureFlag;
use yii\base\InvalidConfigException;

class FFlag implements FeatureFlagObjectModelInterface
{
    public const FF_KEY_LPP_ENABLE = 'lppEnable';
    public const FF_KEY_DEBUG = 'debug';
    public const FF_KEY_LPP_LEAD_CREATED = 'lppLeadCreated';
    public const FF_KEY_ADD_AUTO_QUOTES = 'autoAddQuotes';
    public const FF_KEY_LPP_TO_CLOSED_QUEUE_TRANSFERRING_DAYS_COUNT = 'lppToClosedQueueTransferringDaysCount';
    public const FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES = 'aBTestingEmailOfferTemplates';
    public const FF_KEY_BADGE_COUNT_ENABLE = 'badgeCountEnable';
    public const FF_KEY_PHONE_WIDGET_ACCEPTED_PANEL_ENABLED = 'phoneWidgetAcceptedPanelEnabled';
    public const FF_KEY_OBJECT_SEGMENT_MODULE_ENABLE = 'objectSegmentModuleEnable';
    public const FF_KEY_EMAIL_NORMALIZED_FORM_ENABLE = 'emailNormalizedFormEnable';
    public const FF_KEY_LEAD_TASK_ASSIGN = 'leadTaskAssign';
    public const FF_KEY_SALE_VIEW_IN_LEAD_ENABLE = 'saleViewInLeadEnable';
    public const FF_KEY_BEQ_ENABLE = 'beqEnable';
    public const FF_KEY_BEQ_TO_CLOSED_QUEUE_TRANSFERRING_DAYS_COUNT = 'beqToClosedQueueTransferringDaysCount';
    public const FF_KEY_COMPARE_QUOTE_AND_LEAD_FLIGHT_REQUEST = 'compareQuoteAndLeadFlightRequest';
    public const FF_KEY_HEAT_MAP_AGENT_REPORT_ENABLE = 'heatMapAgentReportEnable';
    public const FF_KEY_EXCLUDE_TAKE_CREATE_FROM_LEAD_USER_CONVERSION_BY_SOURCE_ENABLED = 'excludeTakeCreateFromLeadUserConversionBySourceEnabled';
    public const FF_KEY_UPDATE_PRODUCT_QUOTE_STATUS_BY_BO_SALE_STATUS = 'updateProductQuoteStatusByBOSaleStatus';
    public const FF_KEY_SEND_ADDITIONAL_INFO_TO_BO_ENDPOINTS = 'sendAdditionalInfoToBoEndpoints';
    public const FF_KEY_RETURN_FLIGHT_SEGMENT_AUTOCOMPLETE_ENABLE = 'returnFlightSegmentAutocompleteEnable';
    public const FF_KET_SHIFT_SUMMARY_REPORT_ENABLE = 'shiftSummaryReportEnable';
    public const FF_KEY_BOOKED_QUEUE_CONDITION_BY_DEPARTMENT = 'bookedQueueConditionByDepartment';
    public const FF_KEY_FILTER_USERNAME_ROLES_IN_TRANSFER_TAB = 'filterUsernameAndRolesInTransferTabEnable';
    public const FF_KEY_TELEGRAM_MESSAGE_DELAY_ENABLE = 'telegramMessageDelayEnable';
    public const FF_KEY_SHIFT_SCHEDULE_REQUEST_SAVE_SEND_NOTIFICATION_BY_JOB_ENABLE = 'shiftScheduleRequestSaveSendNotificationByJobEnable';
    public const FF_KEY_FILTER_CONVERSION_DATE_AND_USER_IN_LEAD_SEARCH = 'filterConversionDateAndUserInLeadSearch';
    public const FF_KEY_VALIDATE_ABAC_POLICY_IN_MIGRATION = 'validateAbacPolicyInMigration';
    public const FF_KEY_BUSINESS_QUEUE_LIMIT = 'businessQueueLimit';
    public const FF_KEY_INFO_BLOCK_ENABLE = 'infoBlockEnable';
    public const FF_KEY_SCHEDULE_CHANGE_CLIENT_REMAINDER_NOTIFICATION = 'scheduleChangeClientRemainderNotification';
    public const FF_KEY_VALIDATE_CLOSING_REASON_DUPLICATED = 'validateClosingReasonDuplicated';
    public const FF_KEY_BOOKED_QUEUE_CONDITION_AGENT_IS_OWNER = 'bookedQueueConditionAgentIsAgent';
    public const FF_KEY_SMART_LEAD_DISTRIBUTION_ENABLE = 'smartLeadDistributionEnable';
    public const FF_KEY_USER_SKILL_IN_ABAC_ENABLE = 'userSkillInAbacEnable';
    public const FF_KEY_DISPLAY_SKILL_FIELD_ON_MULTIPLE_UPDATE_USERS = 'displaySkillFieldOnMultipleUpdateUsers';
    public const FF_KEY_REFACTORING_INCOMING_CALL_ENABLE = 'refactoringIncomingCallEnable';
    public const FF_KEY_NEW_USER_TASK_IN_LEAD_VIEW_ENABLE = 'newUserTaskInLeadViewEnable';
    public const FF_KEY_SEGMENT_SIMPLE_LEAD_ENABLE = 'segmentSimpleLead';
    public const FF_KEY_LEAD_TASK_LIST_HISTORY_MODAL_ENABLE = 'leadTaskListHistoryModalEnable';
    public const FF_KEY_DEBUG_ASSIGN_USER_TASK = 'debugAssignUserTaskEnable';
    public const FF_KEY_TWO_FACTOR_AUTH_MODULE = 'twoFactorAuthModule';

    public const FF_KEY_USER_TASK_COMPLETION_DEBUG = 'userTaskCompletionDebug';
    public const FF_KEY_CONVERSION_BY_TIMEZONE = 'conversionByTimezone';
    public const FF_KEY_RESTRICT_EMAIL_TEMPLATE = 'restrictEmailTemplate';
    public const FF_KEY_CHANGE_QUERY_GET_SEND_QUOTE = 'changeQueryGetSendQuote';
    public const FF_KEY_REMOVE_LIMITATION_SUPERVISION_IN_LEAD_SEARCH = 'removeLimitationSupervisionInLeadSearch';
    public const FF_KEY_NO_ANSWER_PROTOCOL_ENABLE = 'noAnswerProtocolEnable';
    public const FF_KEY_USER_CONVERSION_EXCLUDE_ALTERNATIVE_LEAD_ENABLE = 'userConversionExcludeAlternativeLeadEnable';
    public const FF_KEY_SWITCH_NEW_SHIFT_ENABLE = 'switchNewShiftEnable';
    public const FF_KEY_AWARD_ENABLE = 'awardEnable';
    public const FF_KEY_NO_ANSWER_PROTOCOL_CHECK_EMAIL_IN_UNSUBSCRIBE_LIST = 'noAnswerProtocolCheckEmailInUnsubscribeList';
    public const FF_KEY_OBJECT_TASK_STATUS_LOG_ENABLE = 'objectTaskStatusLogEnable';
    public const FF_KEY_QUOTE_MIN_PRICE_ENABLE = 'quoteMinPriceEnable';
    public const FF_KEY_NO_ANSWER_PROTOCOL_SOLD_AUTO_REPLACE_AFK_EMPLOYEE_ENABLE = 'noAnswerProtocolSoldAutoReplaceAfkEmployee';
    public const FF_KEY_SYSTEM_LOG_SEARCH_BLOCK_IMPROVEMENTS_ENABLE = 'systemLogSearchBlockImprovementsEnable';
    public const FF_KEY_BO_API_RBAC_AUTH = 'boApiRbacAuth';
    public const FF_KEY_SAVE_CID_FOR_QUOTES_FROM_SEARCH_ENABLE = 'saveCidForQuotesFromSearchEnable';
    public const FF_KEY_VALIDATE_CHANGE_EXTRA_MARK_UP = 'validateChangeExtraMarkUp';
    public const FF_KEY_UPPERCASE_CONVERT_IN_SET_TRIP_TYPE_METHOD_ENABLE = 'uppercaseConvertInSetTripTypeMethodEnable';
    public const FF_KEY_EXCLUDE_TEST_LEAD_FROM_QUEUES = 'excludeTestLeadFromQueues';
    public const FF_KEY_USER_TASK_COMPLETION_START_END_DT = 'userTaskCompletionStatEndDT';
    public const FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE = 'userNewTaskListOnLeadLogEnable';
    public const FF_KEY_USER_NEW_TASK_LIST_CACHE_DURATION = 'userNewTaskListCacheDuration';
    public const FF_KEY_EXCLUDE_API_CREATE_CASE_VALIDATION = 'excludeApiCreateCaseValidation';
    public const FF_KEY_CROSS_SALE_QUEUE_ENABLE = 'crossSaleQueueEnable';
    public const FF_KEY_CALL_DEBUG_LOG_ENABLE = 'callDebugLog';
    public const FF_KEY_CALL_MISSING_LEAD_OR_CASE = 'callMissingLeadOrCase';
    public const FF_KEY_AUTO_REFRESH_LEAD_TASK_LIST_ENABLE = 'autoRefreshLeadTaskListEnable';
    public const FF_KEY_LOGGING_EXECUTION_TIME_FOR_JOBS_FROM_QUEUE_JOB = 'loggingExecutionTimeForJobsFromQueueJob';
    public const FF_KEY_SHOW_TASK_INFO_COLUMN_ON_LEAD_SECTION_UI = 'showTaskInfoColumnOnLeadSectionUi';
    public const FF_KEY_CROSS_SALE_NEW_PARAMETERS_ENABLE = 'crossSaleNewParametersEnable';
    public const FF_KEY_CROSS_SALE_BADGE_ENABLE = 'crossSaleBadgeEnable';
    public const FF_KEY_ATTACH_LEAD_TO_HOTEL_ORDER = 'attachLeadToHotelOrder';

    public const FF_KEY_LIST = [
        self::FF_KEY_LPP_ENABLE,
        self::FF_KEY_DEBUG,
        self::FF_KEY_LPP_LEAD_CREATED,
        self::FF_KEY_LPP_TO_CLOSED_QUEUE_TRANSFERRING_DAYS_COUNT,
        self::FF_KEY_ADD_AUTO_QUOTES,
        self::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES,
        self::FF_KEY_BADGE_COUNT_ENABLE,
        self::FF_KEY_OBJECT_SEGMENT_MODULE_ENABLE,
        self::FF_KEY_PHONE_WIDGET_ACCEPTED_PANEL_ENABLED,
        self::FF_KEY_EMAIL_NORMALIZED_FORM_ENABLE,
        self::FF_KEY_LEAD_TASK_ASSIGN,
        self::FF_KEY_SALE_VIEW_IN_LEAD_ENABLE,
        self::FF_KEY_COMPARE_QUOTE_AND_LEAD_FLIGHT_REQUEST,
        self::FF_KEY_EXCLUDE_TAKE_CREATE_FROM_LEAD_USER_CONVERSION_BY_SOURCE_ENABLED,
        self::FF_KEY_UPDATE_PRODUCT_QUOTE_STATUS_BY_BO_SALE_STATUS,
        self::FF_KEY_SEND_ADDITIONAL_INFO_TO_BO_ENDPOINTS,
        self::FF_KEY_HEAT_MAP_AGENT_REPORT_ENABLE,
        self::FF_KEY_RETURN_FLIGHT_SEGMENT_AUTOCOMPLETE_ENABLE,
        self::FF_KET_SHIFT_SUMMARY_REPORT_ENABLE,
        self::FF_KEY_BOOKED_QUEUE_CONDITION_BY_DEPARTMENT,
        self::FF_KEY_FILTER_USERNAME_ROLES_IN_TRANSFER_TAB,
        self::FF_KEY_TELEGRAM_MESSAGE_DELAY_ENABLE,
        self::FF_KEY_SHIFT_SCHEDULE_REQUEST_SAVE_SEND_NOTIFICATION_BY_JOB_ENABLE,
        self::FF_KEY_FILTER_CONVERSION_DATE_AND_USER_IN_LEAD_SEARCH,
        self::FF_KEY_VALIDATE_ABAC_POLICY_IN_MIGRATION,
        self::FF_KEY_BUSINESS_QUEUE_LIMIT,
        self::FF_KEY_BEQ_TO_CLOSED_QUEUE_TRANSFERRING_DAYS_COUNT,
        self::FF_KEY_INFO_BLOCK_ENABLE,
        self::FF_KEY_SCHEDULE_CHANGE_CLIENT_REMAINDER_NOTIFICATION,
        self::FF_KEY_VALIDATE_CLOSING_REASON_DUPLICATED,
        self::FF_KEY_BOOKED_QUEUE_CONDITION_AGENT_IS_OWNER,
        self::FF_KEY_SMART_LEAD_DISTRIBUTION_ENABLE,
        self::FF_KEY_USER_SKILL_IN_ABAC_ENABLE,
        self::FF_KEY_DISPLAY_SKILL_FIELD_ON_MULTIPLE_UPDATE_USERS,
        self::FF_KEY_REFACTORING_INCOMING_CALL_ENABLE,
        self::FF_KEY_SEGMENT_SIMPLE_LEAD_ENABLE,
        self::FF_KEY_LEAD_TASK_LIST_HISTORY_MODAL_ENABLE,
        self::FF_KEY_NEW_USER_TASK_IN_LEAD_VIEW_ENABLE,
        self::FF_KEY_DEBUG_ASSIGN_USER_TASK,
        self::FF_KEY_USER_TASK_COMPLETION_DEBUG,
        self::FF_KEY_CONVERSION_BY_TIMEZONE,
        self::FF_KEY_RESTRICT_EMAIL_TEMPLATE,
        self::FF_KEY_CHANGE_QUERY_GET_SEND_QUOTE,
        self::FF_KEY_REMOVE_LIMITATION_SUPERVISION_IN_LEAD_SEARCH,
        self::FF_KEY_NO_ANSWER_PROTOCOL_ENABLE,
        self::FF_KEY_USER_CONVERSION_EXCLUDE_ALTERNATIVE_LEAD_ENABLE,
        self::FF_KEY_SWITCH_NEW_SHIFT_ENABLE,
        self::FF_KEY_AWARD_ENABLE,
        self::FF_KEY_NO_ANSWER_PROTOCOL_CHECK_EMAIL_IN_UNSUBSCRIBE_LIST,
        self::FF_KEY_OBJECT_TASK_STATUS_LOG_ENABLE,
        self::FF_KEY_QUOTE_MIN_PRICE_ENABLE,
        self::FF_KEY_NO_ANSWER_PROTOCOL_SOLD_AUTO_REPLACE_AFK_EMPLOYEE_ENABLE,
        self::FF_KEY_SYSTEM_LOG_SEARCH_BLOCK_IMPROVEMENTS_ENABLE,
        self::FF_KEY_BO_API_RBAC_AUTH,
        self::FF_KEY_SAVE_CID_FOR_QUOTES_FROM_SEARCH_ENABLE,
        self::FF_KEY_VALIDATE_CHANGE_EXTRA_MARK_UP,
        self::FF_KEY_UPPERCASE_CONVERT_IN_SET_TRIP_TYPE_METHOD_ENABLE,
        self::FF_KEY_EXCLUDE_TEST_LEAD_FROM_QUEUES,
        self::FF_KEY_USER_TASK_COMPLETION_START_END_DT,
        self::FF_KEY_USER_NEW_TASK_LIST_ON_LEAD_LOG_ENABLE,
        self::FF_KEY_USER_NEW_TASK_LIST_CACHE_DURATION,
        self::FF_KEY_EXCLUDE_API_CREATE_CASE_VALIDATION,
        self::FF_KEY_CROSS_SALE_QUEUE_ENABLE,
        self::FF_KEY_CALL_DEBUG_LOG_ENABLE,
        self::FF_KEY_CALL_MISSING_LEAD_OR_CASE,
        self::FF_KEY_AUTO_REFRESH_LEAD_TASK_LIST_ENABLE,
        self::FF_KEY_LOGGING_EXECUTION_TIME_FOR_JOBS_FROM_QUEUE_JOB,
        self::FF_KEY_SHOW_TASK_INFO_COLUMN_ON_LEAD_SECTION_UI,
        self::FF_KEY_CROSS_SALE_NEW_PARAMETERS_ENABLE,
        self::FF_KEY_CROSS_SALE_BADGE_ENABLE,
        self::FF_KEY_ATTACH_LEAD_TO_HOTEL_ORDER,
    ];

    public const FF_OBJECT_LIST = [
        self::FF_KEY_DEBUG => DebugFeatureFlag::class,
        //self::FF_KEY_LPP_LEAD_CREATED => UserFeatureFlag::class,
        //self::FF_KEY_PHONE_WIDGET_ACCEPTED_PANEL_ENABLED => DateTimeFeatureFlag::class
    ];

    public const FF_CATEGORY_LEAD               = 'lead';
    public const FF_CATEGORY_SYSTEM             = 'system';
    public const FF_CATEGORY_A_B_TESTING        = 'aBTesting';
    public const FF_CATEGORY_VOIP               = 'voip';
    public const FF_CATEGORY_SHIFT_SCHEDULE     = 'shiftSchedule';

    public const FF_CATEGORY_LIST = [
        self::FF_CATEGORY_LEAD,
        self::FF_CATEGORY_SYSTEM,
        self::FF_CATEGORY_A_B_TESTING,
        self::FF_CATEGORY_VOIP,
        self::FF_CATEGORY_SHIFT_SCHEDULE,
    ];

    /**
     * @return string[]
     */
    public function getKeyList(): array
    {
        return self::FF_KEY_LIST;
    }

    /**
     * @return string[]
     */
    public function getCategoryList(): array
    {
        return self::FF_CATEGORY_LIST;
    }

    /**
     * @param string $objectKey
     * @return object|null
     * @throws InvalidConfigException
     */
    public static function getObjectByKey(string $objectKey): ?object
    {
        if (!empty(self::FF_OBJECT_LIST[$objectKey])) {
            $obj = \Yii::createObject(self::FF_OBJECT_LIST[$objectKey]);
        } else {
            $obj = null;
        }
        return $obj;
    }

    /**
     * @param string $objectKey
     * @return array
     * @throws InvalidConfigException
     */
    public static function getAttributeListByKey(string $objectKey): array
    {
        $list = [];
        $object = self::getObjectByKey($objectKey);
        if ($object) {
            $list = $object::getAttributeList();
        }
        return $list;
    }
}
