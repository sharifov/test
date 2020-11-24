<?php

namespace sales\rbac\rules\lead\view\boExpertBlock;

use common\models\Lead;
use yii\rbac\Rule;

class LeadViewBoExpertBlockEmptyOwnerRule extends Rule
{
    public $name = 'LeadViewBoExpertBlockEmptyOwnerRule';

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
