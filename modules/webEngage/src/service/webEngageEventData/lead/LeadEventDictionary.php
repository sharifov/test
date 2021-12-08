<?php

namespace modules\webEngage\src\service\webEngageEventData\lead;

use common\models\Lead;
use modules\webEngage\settings\WebEngageDictionary;

/**
 * Class LeadEventDictionary
 *
 */
class LeadEventDictionary
{
    public const EVENT_NAME_STATUS_MAP = [
        Lead::STATUS_PENDING => WebEngageDictionary::EVENT_LEAD_CREATED,
        Lead::STATUS_BOOKED => WebEngageDictionary::EVENT_LEAD_BOOKED,
        Lead::STATUS_SOLD => WebEngageDictionary::EVENT_LEAD_SOLD,
        Lead::STATUS_TRASH => WebEngageDictionary::EVENT_LEAD_TRASHED,
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
