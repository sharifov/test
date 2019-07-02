<?php

namespace sales\rbac\rules;

use common\models\Lead;

class LeadOwnerRule extends LeadRule
{
    public $name = 'isLeadOwner';

    public function getData(int $userId, Lead $lead): bool
    {
        return $lead->canAgentEdit($userId);
    }
}