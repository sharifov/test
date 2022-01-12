<?php

namespace src\model\leadRedial\assign;

use common\models\Lead;

interface Users
{
    public function getUsers(Lead $lead, int $limitUsers, bool $enabledSortingForBusinessLead): array;
}
