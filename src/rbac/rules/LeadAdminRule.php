<?php

namespace src\rbac\rules;

use common\models\Lead;

class LeadAdminRule extends LeadRule
{
    public $name = 'isLeadAdmin';

    public function getData(int $userId, Lead $lead): bool
    {
        return $lead->canAdminEdit();
    }
}
