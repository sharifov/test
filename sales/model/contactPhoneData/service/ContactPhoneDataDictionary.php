<?php

namespace sales\model\contactPhoneData\service;

/**
 * Class ContactPhoneDataDictionary
 */
class ContactPhoneDataDictionary
{
    public const KEY_IS_TRUSTED = 'is_trusted';
    public const KEY_ALLOW_LIST = 'allow_list';

    public const KEY_LIST = [
        self::KEY_IS_TRUSTED => self::KEY_IS_TRUSTED,
        self::KEY_ALLOW_LIST => self::KEY_ALLOW_LIST,
    ];
}
