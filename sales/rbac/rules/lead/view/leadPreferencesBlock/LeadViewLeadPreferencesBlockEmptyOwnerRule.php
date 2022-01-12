<?php

namespace sales\rbac\rules\lead\view\leadPreferencesBlock;

use common\models\Lead;
use yii\rbac\Rule;

class LeadViewLeadPreferencesBlockEmptyOwnerRule extends Rule
{
    public $name = 'LeadViewLeadPreferencesBlockEmptyOwnerRule';

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
