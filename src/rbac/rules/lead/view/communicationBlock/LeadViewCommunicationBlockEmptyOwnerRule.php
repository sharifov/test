<?php

namespace src\rbac\rules\lead\view\communicationBlock;

use common\models\Lead;
use yii\rbac\Rule;

class LeadViewCommunicationBlockEmptyOwnerRule extends Rule
{
    public $name = 'LeadViewCommunicationBlockEmptyOwnerRule';

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
