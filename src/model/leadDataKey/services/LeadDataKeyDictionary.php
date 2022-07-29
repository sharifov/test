<?php

namespace src\model\leadDataKey\services;

/**
 * Class LeadDataKeyDictionary
 */
class LeadDataKeyDictionary
{
    public const KEY_CROSS_SYSTEM_XP = 'cross_system_xp';
    public const KEY_WE_EMAIL_REPLIED = 'we_email_replied';
    public const KEY_WE_FIRST_CALL_NOT_PICKED = 'we_first_call_not_picked';
    public const KEY_LPP_EXCLUDE = 'lpp_exclude';
    public const KEY_CREATED_BY_CALL_ID = 'created_by_call_id';
    public const KEY_LEAD_OBJECT_SEGMENT = 'lead_object_segment';
    public const KEY_LEAD_RATING_POINTS = 'lead_rating_points';
    public const KEY_LEAD_RATING_CATEGORY = 'lead_rating_category';

    public const KEY_LIST = [
        self::KEY_CROSS_SYSTEM_XP => self::KEY_CROSS_SYSTEM_XP,
        self::KEY_WE_EMAIL_REPLIED => self::KEY_WE_EMAIL_REPLIED,
        self::KEY_WE_FIRST_CALL_NOT_PICKED => self::KEY_WE_FIRST_CALL_NOT_PICKED,
        self::KEY_LPP_EXCLUDE => self::KEY_LPP_EXCLUDE,
        self::KEY_CREATED_BY_CALL_ID => self::KEY_CREATED_BY_CALL_ID,
        self::KEY_LEAD_OBJECT_SEGMENT => self::KEY_LEAD_OBJECT_SEGMENT,
        self::KEY_LEAD_RATING_POINTS => self::KEY_LEAD_RATING_POINTS,
        self::KEY_LEAD_RATING_CATEGORY => self::KEY_LEAD_RATING_CATEGORY,
    ];
}
