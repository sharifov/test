<?php

namespace sales\model\smsSubscribe\entity;

/**
 * Class SmsSubscribeStatus
 */
class SmsSubscribeStatus
{
    public const STATUS_NEW = 1;
    public const STATUS_PENDING = 2;
    public const STATUS_SUBSCRIBED = 3;
    public const STATUS_UNSUBSCRIBED = 4;

    public const STATUS_LIST = [
        self::STATUS_NEW => 'new',
        self::STATUS_PENDING => 'pending',
        self::STATUS_SUBSCRIBED => 'subscribed',
        self::STATUS_UNSUBSCRIBED => 'unsubscribed',
    ];

    public static function getStatusName(?int $statusId): string
    {
        return self::STATUS_LIST[$statusId] ?? '-';
    }
}
