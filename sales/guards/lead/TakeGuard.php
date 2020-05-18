<?php

namespace sales\guards\lead;

use common\models\Employee;

class TakeGuard
{

    public function frequencyMinutesGuard(Employee $user, array $flowDescriptions = [], array $fromStatuses = []): void
    {
        $isAccessNewLeadByFrequency = $user->accessTakeLeadByFrequencyMinutes($flowDescriptions, $fromStatuses);
        if (!$isAccessNewLeadByFrequency['access']) {
            throw new \DomainException('Access is denied (frequency)');
        }
    }

    public function minPercentGuard(Employee $user, array $flowDescriptions = []): void
    {
        $isAccessNewLead = $user->accessTakeNewLead($flowDescriptions);
        if (!$isAccessNewLead) {
            throw new \DomainException('Access is denied (limit)');
        }
    }

    public function shiftTimeGuard(Employee $user): void
    {
        if (!$user->checkShiftTime()) {
            throw new \DomainException('New leads are only available on your shift.');
        }
    }
}
