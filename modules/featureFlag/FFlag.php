<?php

namespace modules\featureFlag;

use yii\base\Module;

class FFlag
{
    public const FF_KEY_LPP_ENABLE = 'lppEnable';
    public const FF_KEY_DEBUG = 'debug';
    public const FF_KEY_LPP_LEAD_CREATED = 'lppLeadCreated';
    public const FF_KEY_ADD_AUTO_QUOTES = 'autoAddQuotes';
    public const FF_KEY_LPP_TO_CLOSED_QUEUE_TRANSFERRING_DAYS_COUNT = 'lppToClosedQueueTransferringDaysCount';

    public const FF_KEY_LIST = [
        self::FF_KEY_LPP_ENABLE => self::FF_KEY_LPP_ENABLE,
        self::FF_KEY_DEBUG => self::FF_KEY_DEBUG,
        self::FF_KEY_LPP_LEAD_CREATED => self::FF_KEY_LPP_LEAD_CREATED,
        self::FF_KEY_LPP_TO_CLOSED_QUEUE_TRANSFERRING_DAYS_COUNT => self::FF_KEY_LPP_TO_CLOSED_QUEUE_TRANSFERRING_DAYS_COUNT,
        self::FF_KEY_ADD_AUTO_QUOTES => self::FF_KEY_ADD_AUTO_QUOTES,
    ];

    public const FF_CATEGORY_LEAD = 'lead';
    public const FF_CATEGORY_SYSTEM = 'system';

    public const FF_CATEGORY_LIST = [
        self::FF_CATEGORY_LEAD => self::FF_CATEGORY_LEAD,
        self::FF_CATEGORY_SYSTEM => self::FF_CATEGORY_SYSTEM,
    ];
}
