<?php

namespace src\model\leadRedial\queue;

use common\models\Employee;

class TestLeadRedialQueue implements LeadRedialQueue
{
    public function getCall(Employee $user): ?RedialCall
    {
        return new RedialCall(
            '+14157693509',
            1468,
            '+37369305726',
            2,
            513195
        );
    }
}
