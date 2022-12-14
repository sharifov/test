<?php

namespace src\model\leadRedial\assign;

use common\models\Lead;
use common\models\query\EmployeeQuery;
use src\helpers\setting\SettingHelper;

class EmployeeUsers implements Users
{
    public function getUsers(Lead $lead, int $limitUsers, bool $enabledSortingForBusinessLead): array
    {
        return EmployeeQuery::getAgentsForRedialCallByLead(
            $enabledSortingForBusinessLead,
            $lead->project_id,
            $lead->l_dep_id,
            SettingHelper::getRedialUserAccessExpiredSecondsLimit(),
            $limitUsers
        );
    }
}
