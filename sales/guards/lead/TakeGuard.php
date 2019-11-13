<?php

namespace sales\guards\lead;

use common\models\Employee;

class TakeGuard
{

    /**
     * @param Employee $user
     * @param array $flowDescriptions
     */
    public function frequencyMinutesGuard(Employee $user, array $flowDescriptions = []): void
    {
        $isAccessNewLeadByFrequency = $user->accessTakeLeadByFrequencyMinutes($flowDescriptions);
        if (!$isAccessNewLeadByFrequency['access']) {
            throw new \DomainException('Access is denied (frequency)');
        }
    }

    /**
     * @param Employee $user
     * @param array $flowDescriptions
     */
    public function minPercentGuard(Employee $user, array $flowDescriptions = []): void
    {
        $isAccessNewLead = $user->accessTakeNewLead($flowDescriptions);
        if (!$isAccessNewLead) {
            throw new \DomainException('Access is denied (limit)');
        }
    }
}
