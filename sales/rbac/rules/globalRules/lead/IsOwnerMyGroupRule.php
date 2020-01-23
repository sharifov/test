<?php

namespace sales\rbac\rules\globalRules\lead;

use common\models\Lead;
use sales\access\EmployeeGroupAccess;
use yii\rbac\Rule;

class IsOwnerMyGroupRule extends Rule
{
    public $name = 'global/lead/isOwnerMyGroupRule';

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
