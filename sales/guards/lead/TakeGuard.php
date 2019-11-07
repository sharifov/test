<?php

namespace sales\guards\lead;

use common\models\Employee;

class TakeGuard
{
    /**
     * @param Employee $user
     * @throws \DomainException
     */
    public function frequencyMinutesGuard(Employee $user): void
    {
        $isAccessNewLeadByFrequency = $user->accessTakeLeadByFrequencyMinutes();
        if (!$isAccessNewLeadByFrequency['access']) {
            throw new \DomainException('Access is denied (frequency)');
        }
    }

    /**
     * @param Employee $user
     * @throws \DomainException
     */
    public function minPercentGuard(Employee $user): void
    {
        $isAccessNewLead = $user->accessTakeNewLead();
        if (!$isAccessNewLead) {
            throw new \DomainException('Access is denied (limit)');
        }
    }
}
