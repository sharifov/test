<?php

namespace sales\rbac\rules\lead\view\taskListBlock;

use common\models\Lead;
use yii\rbac\Rule;

class LeadViewTaskListBlockEmptyOwnerRule extends Rule
{
    public $name = 'LeadViewTaskListBlockEmptyOwnerRule';

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
