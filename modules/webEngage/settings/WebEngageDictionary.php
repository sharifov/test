<?php

namespace modules\webEngage\settings;

/**
 * Class WebEngageDictionary
 */
class WebEngageDictionary
{
    public const ENDPOINT_EVENTS = 'events';
    public const ENDPOINT_EVENT_LIST = ['events'];

    public const EVENT_LEAD_CREATED = 'LeadCreated';
    public const EVENT_LEAD_BOOKED = 'LeadBooked';
    public const EVENT_LEAD_SOLD = 'LeadSold';
    public const EVENT_LEAD_TRASHED = 'LeadTrashed ';
    public const EVENT_CALL_FIRST_CALL_NOT_PICKED = 'FirstCallNotPicked';
    public const EVENT_CALL_USER_PICKED_CALL = 'UserPickedCall';

    public const EVENT_LIST = [
        self::EVENT_LEAD_CREATED => self::EVENT_LEAD_CREATED,
        self::EVENT_LEAD_BOOKED => self::EVENT_LEAD_BOOKED,
        self::EVENT_LEAD_SOLD => self::EVENT_LEAD_SOLD,
        self::EVENT_LEAD_TRASHED => self::EVENT_LEAD_TRASHED,
        self::EVENT_CALL_FIRST_CALL_NOT_PICKED => self::EVENT_CALL_FIRST_CALL_NOT_PICKED,
        self::EVENT_CALL_USER_PICKED_CALL => self::EVENT_CALL_USER_PICKED_CALL,
    ];
}