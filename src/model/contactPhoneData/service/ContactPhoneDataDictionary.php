<?php

namespace src\model\contactPhoneData\service;

/**
 * Class ContactPhoneDataDictionary
 */
class ContactPhoneDataDictionary
{
    public const KEY_IS_TRUSTED = 'is_trusted';
    public const KEY_ALLOW_LIST = 'allow_list';
    public const KEY_AUTO_CREATE_LEAD_OFF = 'auto_create_lead_off';
    public const KEY_AUTO_CREATE_CASE_OFF = 'auto_create_case_off';
    public const KEY_INVALID = 'invalid';

    public const KEY_LIST = [
        self::KEY_IS_TRUSTED => 'Is Trusted',
        self::KEY_ALLOW_LIST => 'Allow List',
        self::KEY_AUTO_CREATE_LEAD_OFF => 'Auto create lead off',
        self::KEY_AUTO_CREATE_CASE_OFF => 'Auto create case off',
        self::KEY_INVALID => 'Invalid',
    ];

    public const KEY_LIST_CLASS = [
        self::KEY_IS_TRUSTED            => 'll-pending',
        self::KEY_ALLOW_LIST            => 'll-sold',
        self::KEY_AUTO_CREATE_LEAD_OFF  => 'badge-secondary',
        self::KEY_AUTO_CREATE_CASE_OFF  => 'badge-dark',
        self::KEY_INVALID  => 'badge-dark',
    ];

    public const DEFAULT_TRUE_VALUE = '1';
}
