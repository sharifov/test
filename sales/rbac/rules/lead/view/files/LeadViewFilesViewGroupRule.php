<?php

namespace sales\rbac\rules\lead\view\files;

use common\models\Lead;
use sales\access\EmployeeGroupAccess;
use yii\rbac\Rule;

class LeadViewFilesViewGroupRule extends Rule
{
    public $name = 'LeadViewFilesViewGroupRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['lead']) || !$params['lead'] instanceof Lead) {
            return false;
        }

        $lead = $params['lead'];

        if (!$lead->hasOwner()) {
            return false;
        }

        return EmployeeGroupAccess::isUserInCommonGroup($userId, $lead->employee_id);
    }
}
