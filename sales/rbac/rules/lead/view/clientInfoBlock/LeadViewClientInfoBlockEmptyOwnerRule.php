<?php

namespace sales\rbac\rules\lead\view\clientInfoBlock;

use common\models\Lead;
use yii\rbac\Rule;

class LeadViewClientInfoBlockEmptyOwnerRule extends Rule
{
    public $name = 'LeadViewClientInfoBlockEmptyOwnerRule';

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
