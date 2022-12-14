<?php

namespace src\rbac\rules\lead\manage;

use common\models\Lead;
use yii\rbac\Rule;

class LeadManageEmptyOwnerRule extends Rule
{
    public $name = 'LeadManageEmptyOwnerRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['lead']) || !$params['lead'] instanceof Lead) {
            return false;
        }

        /** @var Lead $lead */
        $lead = $params['lead'];

        return !$lead->hasOwner();
    }
}
