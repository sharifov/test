<?php

namespace sales\rbac\rules\lead\view\leadPreferencesBlock;

use common\models\Lead;
use src\access\EmployeeGroupAccess;
use yii\rbac\Rule;

class LeadViewLeadPreferencesBlockGroupRule extends Rule
{
    public $name = 'LeadViewLeadPreferencesBlockGroupRule';

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
