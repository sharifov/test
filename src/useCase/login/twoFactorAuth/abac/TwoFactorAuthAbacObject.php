<?php

namespace src\useCase\login\twoFactorAuth\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class TwoFactorAuthAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'two-factor/two-factor/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** ACTION PERMISSION */
    public const TWO_FACTOR_AUTH  = self::NS . 'act/two-factor-auth';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::TWO_FACTOR_AUTH => self::TWO_FACTOR_AUTH,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS = 'access';
    public const ACTION_TOTP  = 'totpAuth';
    public const ACTION_OTP_EMAIL  = 'otpEmail';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::TWO_FACTOR_AUTH => [self::ACTION_ACCESS, self::ACTION_TOTP, self::ACTION_OTP_EMAIL],
    ];

    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [];

    public static function getObjectList(): array
    {
        return self::OBJECT_LIST;
    }

    public static function getObjectActionList(): array
    {
        return self::OBJECT_ACTION_LIST;
    }

    public static function getObjectAttributeList(): array
    {
        return self::OBJECT_ATTRIBUTE_LIST;
    }
}
