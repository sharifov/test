<?php

namespace modules\lead\src\abac\dto;

use common\models\Lead;

/**
 * @property bool $is_owner
 */
class LeadAbacDto extends \stdClass
{
    public bool $is_owner;

    /**
     * @param Lead|null $lead
     * @param int $userId
     */
    public function __construct(?Lead $lead, int $userId)
    {
        if ($lead) {
            $this->is_owner = $lead->isOwner($userId);
        }
    }
}
