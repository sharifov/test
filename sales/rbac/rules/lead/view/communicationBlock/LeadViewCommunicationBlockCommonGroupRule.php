<?php

namespace sales\rbac\rules\lead\view\communicationBlock;

use common\models\Lead;
use sales\access\EmployeeGroupAccess;
use yii\rbac\Rule;

class LeadViewCommunicationBlockCommonGroupRule extends Rule
{
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
