<?php

namespace modules\webEngage\src\service\webEngageEventData\lead;

use common\models\Lead;

/**
 * Class LeadEventDictionary
 *
 */
class LeadEventDictionary
{
    public const EVENT_NAME_LEAD_CREATED = 'LeadCreated';
    public const EVENT_NAME_LEAD_BOOKED = 'LeadBooked';
    public const EVENT_NAME_LEAD_SOLD = 'LeadSold';
    public const EVENT_NAME_LEAD_TRASHED = 'LeadTrashed';

    public const EVENT_NAME_STATUS_MAP = [
        Lead::STATUS_PENDING => self::EVENT_NAME_LEAD_CREATED,
        Lead::STATUS_BOOKED => self::EVENT_NAME_LEAD_BOOKED,
        Lead::STATUS_SOLD => self::EVENT_NAME_LEAD_SOLD,
        Lead::STATUS_TRASH => self::EVENT_NAME_LEAD_TRASHED,
    ];

    public const STATUS_PROCESSED_LIST = [
        Lead::STATUS_PENDING => Lead::STATUS_PENDING,
        Lead::STATUS_BOOKED => Lead::STATUS_BOOKED,
        Lead::STATUS_SOLD => Lead::STATUS_SOLD,
        Lead::STATUS_TRASH => Lead::STATUS_TRASH,
    ];

    public static function getEventNameByStatus(int $status): ?string
    {
        return self::EVENT_NAME_STATUS_MAP[$status] ?? null;
    }
}
