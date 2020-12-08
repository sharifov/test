<?php

namespace sales\services\quote\addQuote\guard;

use common\models\Lead;

/**
 * Class LeadGuard
 */
class LeadGuard
{
    /**
     * @param int|null $leadId
     * @return Lead
     */
    public static function guard(?int $leadId): Lead
    {
        if (!$lead = Lead::findOne(['id' => $leadId])) {
            throw new \DomainException('Lead id(' . $leadId . ') not found');
        }
        return $lead;
    }
}
