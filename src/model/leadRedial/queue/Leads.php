<?php

namespace src\model\leadRedial\queue;

use common\models\Employee;

interface Leads
{
    public function getLeads(Employee $user): array;
}
