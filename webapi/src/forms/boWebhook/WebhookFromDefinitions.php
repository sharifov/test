<?php

namespace webapi\src\forms\boWebhook;

interface WebhookFromDefinitions
{
    const STATUS_PENDING = 'Pending';
    const STATUS_PROCESSING = 'Processing';
    const STATUS_EXCHANGED = 'Exchanged';
    const STATUS_REFUNDED = 'Refunded';
    const STATUS_CANCELED = 'Canceled';
    const EXCHANGE_STATUS_LIST = [
        self::STATUS_PENDING => self::STATUS_PENDING,
        self::STATUS_EXCHANGED => self::STATUS_EXCHANGED,
        self::STATUS_CANCELED => self::STATUS_CANCELED,
        self::STATUS_PROCESSING => self::STATUS_PROCESSING,
    ];
    const REFUND_STATUS_LIST = [
        self::STATUS_PENDING => self::STATUS_PENDING,
        self::STATUS_REFUNDED => self::STATUS_REFUNDED,
        self::STATUS_CANCELED => self::STATUS_CANCELED,
        self::STATUS_PROCESSING => self::STATUS_PROCESSING,
    ];
}
