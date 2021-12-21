<?php

namespace sales\model\leadDataKey\services;

/**
 * Class LeadDataKeyDictionary
 */
class LeadDataKeyDictionary
{
    public const KEY_CROSS_SYSTEM_XP = 'cross_system_xp';
    public const KEY_WE_EMAIL_REPLIED = 'we_email_replied';
    public const KEY_WE_FIRST_CALL_NOT_PICKED = 'we_first_call_not_picked';

    public const KEY_LIST = [
        self::KEY_CROSS_SYSTEM_XP => self::KEY_CROSS_SYSTEM_XP,
        self::KEY_WE_EMAIL_REPLIED => self::KEY_WE_EMAIL_REPLIED,
        self::KEY_WE_FIRST_CALL_NOT_PICKED => self::KEY_WE_FIRST_CALL_NOT_PICKED,
    ];
}
