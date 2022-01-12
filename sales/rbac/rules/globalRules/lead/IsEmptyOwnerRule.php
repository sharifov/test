<?php

namespace sales\rbac\rules\globalRules\lead;

use common\models\Lead;
use yii\rbac\Rule;

class IsEmptyOwnerRule extends Rule
{
    public $name = 'global/lead/isEmptyOwnerRule';

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
