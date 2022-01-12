<?php

namespace src\model\leadRedial\queue;

use common\models\Employee;

class TestLeads implements Leads
{
    public function getLeads(Employee $user): array
    {
        return [513195];
    }
}
