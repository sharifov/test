<?php

namespace src\rbac\rules\cases\view\files;

use src\access\EmployeeGroupAccess;
use src\entities\cases\Cases;
use yii\rbac\Rule;

class CaseViewFilesViewGroupRule extends Rule
{
    public $name = 'CaseViewFilesViewGroupRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['case']) || !$params['case'] instanceof Cases) {
            return false;
        }

        $case = $params['case'];

        if ($case->isFreedOwner()) {
            return false;
        }

        return EmployeeGroupAccess::isUserInCommonGroup($userId, $case->cs_user_id);
    }
}
