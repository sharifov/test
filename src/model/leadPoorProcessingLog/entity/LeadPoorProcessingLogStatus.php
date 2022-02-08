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

    public const REASON_CALL = 'Call';
    public const REASON_EMAIL = 'Email';
    public const REASON_CHANGE_STATUS = 'Lead changes status from %s to %s';
    public const REASON_CHANGE_LAST_ACTION = 'Change last action';
    public const REASON_CHANGE_OWNER = 'Owner changed from %s to %s';
    public const REASON_SMS = 'SMS';
    public const REASON_CALL_EXPERT = 'Call Expert';
    public const REASON_NOTE = 'Note';
    public const REASON_QUOTE = 'Quote';
    public const REASON_LEAD_TACK = 'Lead Task';
    public const REASON_TAKE = 'Take';
    public const REASON_EXPERT_IDLE = 'Expert Idle';
}
