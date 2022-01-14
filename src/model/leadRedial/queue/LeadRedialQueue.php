<?php

namespace src\model\leadRedial\queue;

use common\models\Employee;

interface LeadRedialQueue
{
    public function getCall(Employee $user): ?RedialCall;
}
