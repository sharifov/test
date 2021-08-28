<?php

namespace sales\model\leadUserConversion\service;

/**
 * Class LeadUserConversionDictionary
 */
class LeadUserConversionDictionary
{
    public const DESCRIPTION_TAKE = 'Take';
    public const DESCRIPTION_CALL_AUTO_TAKE = 'Call Auto Take';
    public const DESCRIPTION_MANUAL = 'Manual';
    public const DESCRIPTION_CLIENT_CHAT_MANUAL = 'Client Chat Manual';
    public const DESCRIPTION_TAKE_OVER = 'Take over';
    public const DESCRIPTION_CLONE = 'Clone';
    public const DESCRIPTION_ASSIGN = 'Assign';

    public const DESCRIPTION_LIST = [
        self::DESCRIPTION_MANUAL => self::DESCRIPTION_MANUAL,
        self::DESCRIPTION_TAKE => self::DESCRIPTION_TAKE,
        self::DESCRIPTION_CALL_AUTO_TAKE => self::DESCRIPTION_CALL_AUTO_TAKE,
        self::DESCRIPTION_CLIENT_CHAT_MANUAL => self::DESCRIPTION_CLIENT_CHAT_MANUAL,
        self::DESCRIPTION_TAKE_OVER => self::DESCRIPTION_TAKE_OVER,
        self::DESCRIPTION_CLONE => self::DESCRIPTION_CLONE,
        self::DESCRIPTION_ASSIGN => self::DESCRIPTION_ASSIGN,
    ];
}
