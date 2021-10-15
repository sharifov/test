<?php

namespace sales\model\leadRedial\queue;

use common\models\Employee;

class TestLeads implements Leads
{
    public function getLeads(Employee $user): array
    {
        return [513195];
    }
}
