<?php

namespace src\model\leadUserData\entity;

use common\models\Employee;
use common\models\Lead;
use Yii;

/**
 * Class LeadUserDataDictionary
 */
class LeadUserDataDictionary
{
    public const TYPE_CALL_OUT = 1;
    public const TYPE_SMS_OUT = 2;
    public const TYPE_EMAIL_OFFER = 3;

    public const TYPE_LIST = [
        self::TYPE_CALL_OUT => 'Call Out',
        self::TYPE_SMS_OUT => 'SMS Out',
        self::TYPE_EMAIL_OFFER => 'Email offer',
    ];

    public static function getTypeName(?int $typeId): string
    {
        return self::TYPE_LIST[$typeId] ?? '-';
    }
}
