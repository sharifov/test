<?php

namespace sales\rbac\rules\lead\manage;

use common\models\Lead;
use yii\rbac\Rule;

class LeadManageIsOwnerRule extends Rule
{
    public $name = 'LeadManageIsOwnerRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['lead']) || !$params['lead'] instanceof Lead) {
            return false;
        }

        /** @var Lead $lead */
        $lead = $params['lead'];

        return $lead->isOwner($userId);
    }
}
