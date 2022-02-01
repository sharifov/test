<?php

namespace src\model\leadPoorProcessingLog\entity;

/**
 * Class LeadPoorProcessingLogStatus
 */
class LeadPoorProcessingLogStatus
{
    public const STATUS_CREATED = 1;
    public const STATUS_UPDATED = 2;
    public const STATUS_DELETED = 3;
    public const STATUS_ADDED_TO_EXTRA_QUEUE = 4;

    public const STATUS_LIST = [
        self::STATUS_CREATED => 'Created',
        self::STATUS_UPDATED => 'Updated',
        self::STATUS_DELETED => 'Deleted',
        self::STATUS_ADDED_TO_EXTRA_QUEUE => 'Added to Extra queue',
    ];
}
