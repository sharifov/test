<?php

namespace src\model\leadRedial\assign;

use common\models\Lead;

class TestUsers implements Users
{
    public function getUsers(Lead $lead, int $limitUsers, bool $enabledSortingForBusinessLead): array
    {
        return [295];
    }
}
