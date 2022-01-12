<?php

namespace src\model\leadRedial\queue;

use common\models\Employee;

class NullLeadRedialQueue implements LeadRedialQueue
{
    public function getCall(Employee $user): ?RedialCall
    {
        return null;
    }
}
