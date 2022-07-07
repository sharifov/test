<?php

namespace modules\featureFlag;

use kivork\FeatureFlag\Models\flags\dateTime\DateTimeFeatureFlag;
use modules\featureFlag\models\debug\DebugFeatureFlag;
use modules\featureFlag\models\user\UserFeatureFlag;
use yii\base\InvalidConfigException;

class FFlag
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
    public const FF_KEY_LEAD_TASK_ASSIGN = 'leadTaskAssign';
    public const FF_KEY_SALE_VIEW_IN_LEAD_ENABLE = 'saleViewInLeadEnable';
    public const FF_KEY_BEQ_ENABLE = 'beqEnable';
    public const FF_KEY_COMPARE_QUOTE_AND_LEAD_FLIGHT_REQUEST = 'compareQuoteAndLeadFlightRequest';
    public const FF_KEY_EXCLUDE_TAKE_CREATE_FROM_LEAD_USER_CONVERSION_BY_SOURCE_ENABLED = 'excludeTakeCreateFromLeadUserConversionBySourceEnabled';

    public const FF_KEY_LIST = [
        self::FF_KEY_LPP_ENABLE => self::FF_KEY_LPP_ENABLE,
        self::FF_KEY_DEBUG => self::FF_KEY_DEBUG,
        self::FF_KEY_LPP_LEAD_CREATED => self::FF_KEY_LPP_LEAD_CREATED,
        self::FF_KEY_LPP_TO_CLOSED_QUEUE_TRANSFERRING_DAYS_COUNT => self::FF_KEY_LPP_TO_CLOSED_QUEUE_TRANSFERRING_DAYS_COUNT,
        self::FF_KEY_ADD_AUTO_QUOTES => self::FF_KEY_ADD_AUTO_QUOTES,
        self::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES => self::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES,
        self::FF_KEY_BADGE_COUNT_ENABLE => self::FF_KEY_BADGE_COUNT_ENABLE,
        self::FF_KEY_OBJECT_SEGMENT_MODULE_ENABLE => self::FF_KEY_OBJECT_SEGMENT_MODULE_ENABLE,
        self::FF_KEY_PHONE_WIDGET_ACCEPTED_PANEL_ENABLED => self::FF_KEY_PHONE_WIDGET_ACCEPTED_PANEL_ENABLED,
        self::FF_KEY_LEAD_TASK_ASSIGN => self::FF_KEY_LEAD_TASK_ASSIGN,
        self::FF_KEY_SALE_VIEW_IN_LEAD_ENABLE => self::FF_KEY_SALE_VIEW_IN_LEAD_ENABLE,
        self::FF_KEY_COMPARE_QUOTE_AND_LEAD_FLIGHT_REQUEST => self::FF_KEY_COMPARE_QUOTE_AND_LEAD_FLIGHT_REQUEST,
        self::FF_KEY_EXCLUDE_TAKE_CREATE_FROM_LEAD_USER_CONVERSION_BY_SOURCE_ENABLED => self::FF_KEY_EXCLUDE_TAKE_CREATE_FROM_LEAD_USER_CONVERSION_BY_SOURCE_ENABLED,
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

    public const FF_CATEGORY_LIST = [
        self::FF_CATEGORY_LEAD,
        self::FF_CATEGORY_SYSTEM,
        self::FF_CATEGORY_A_B_TESTING,
        self::FF_CATEGORY_VOIP,
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
