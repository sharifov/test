<?php

namespace sales\rbac\rules\lead\manage;

use common\models\Lead;
use sales\access\EmployeeGroupAccess;
use yii\rbac\Rule;

class LeadManageGroupRule extends Rule
{
    public $name = 'LeadManageGroupRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['lead']) || !$params['lead'] instanceof Lead) {
            return false;
        }

        /** @var Lead $lead */
        $lead = $params['lead'];

        if ($leadOwnerId = $lead->employee_id) {
            return EmployeeGroupAccess::isUserInCommonGroup($userId, $leadOwnerId);
        }

        return false;
    }
}
