<?php

namespace sales\services\badges;

/**
 * Class BadgesDictionary
 */
class BadgesDictionary
{
    public const KEY_OBJECT_LEAD = 'lead';
    public const KEY_OBJECT_CASES = 'cases';
    public const KEY_OBJECT_VOICE_MAIL = 'voiceMail';
    public const KEY_OBJECT_ORDER = 'order';
    public const KEY_OBJECT_QA_TASK = 'qaTask';

    public const KEY_OBJECT_LIST = [
        self::KEY_OBJECT_LEAD => self::KEY_OBJECT_LEAD,
        self::KEY_OBJECT_CASES => self::KEY_OBJECT_CASES,
        self::KEY_OBJECT_VOICE_MAIL => self::KEY_OBJECT_VOICE_MAIL,
        self::KEY_OBJECT_ORDER => self::KEY_OBJECT_ORDER,
        self::KEY_OBJECT_QA_TASK => self::KEY_OBJECT_QA_TASK,
    ];
}
