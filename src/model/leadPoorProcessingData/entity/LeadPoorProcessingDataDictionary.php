<?php

namespace src\model\leadPoorProcessingData\entity;

/**
 * Class LeadPoorProcessingDataDictionary
 */
class LeadPoorProcessingDataDictionary
{
    public const KEY_NO_ACTION = 'no_action';
    public const KEY_LAST_ACTION = 'last_action';
    public const KEY_EXTRA_TO_PROCESSING_TAKE = 'extra_to_processing_take';
    public const KEY_EXTRA_TO_PROCESSING_MULTIPLE_UPD = 'extra_to_processing_multiple_update';
    public const KEY_SCHEDULED_COMMUNICATION = 'scheduled_communication';
    public const KEY_EXPERT_IDLE = 'expert_idle';
    public const KEY_SEND_SMS_OFFER = 'send_sms_offer';

    public const KEY_LIST = [
        self::KEY_NO_ACTION => 'No action',
        self::KEY_LAST_ACTION => 'Last action',
        self::KEY_EXTRA_TO_PROCESSING_TAKE => 'Extra to Processing Take',
        self::KEY_EXTRA_TO_PROCESSING_MULTIPLE_UPD => 'Extra to Processing Multiple Update',
        self::KEY_SCHEDULED_COMMUNICATION => 'Scheduled communication',
        self::KEY_EXPERT_IDLE => 'Expert Idle',
        self::KEY_SEND_SMS_OFFER => 'Send SMS Offer',
    ];
}
