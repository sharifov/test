<?php

namespace sales\guards;

use common\models\Employee;
use common\models\Lead;

class LeadEditGuard
{

    /**
     * @param Lead $lead
     * @param Employee $user
     */
    public function guard(Lead $lead, Employee $user): void
    {
        if ($user->isAdmin()) {
            return;
        }
        if ($lead->isOwner($user->id, false)) {
            return;
        }
        if ($user->isAgent()) {
            throw new \DomainException('Cant access for edit Lead');
        }
    }

}
