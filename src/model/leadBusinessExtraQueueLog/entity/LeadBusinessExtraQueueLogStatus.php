<?php

namespace src\model\leadBusinessExtraQueueLog\entity;

class LeadBusinessExtraQueueLogStatus
{
    public const STATUS_CREATED = 1;
    public const STATUS_UPDATED = 2;
    public const STATUS_DELETED = 3;
    public const STATUS_ADDED_TO_BUSINESS_EXTRA_QUEUE = 4;

    public const STATUS_LIST = [
        self::STATUS_CREATED => 'Created',
        self::STATUS_UPDATED => 'Updated',
        self::STATUS_DELETED => 'Deleted',
        self::STATUS_ADDED_TO_BUSINESS_EXTRA_QUEUE => 'Added to Business Extra queue',
    ];

    public const REASON_CALL = 'Call';
    public const REASON_RECEIVED_EMAIL = 'Received Email';
    public const REASON_RECEIVED_SMS = 'Received SMS';
    public const REASON_RECEIVED_MESSAGE_FROM_CHAT = 'Received Message from chat';
    public const REASON_CHANGE_STATUS = 'Lead changes status from %s to %s';
    public const REASON_ADDED_TO_BUSINESS_EXTRA_QUEUE_DUE_EXPIRATION_TIME = 'Time expired';
}
