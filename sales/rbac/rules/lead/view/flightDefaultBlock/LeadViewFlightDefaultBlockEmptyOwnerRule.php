<?php

namespace sales\rbac\rules\lead\view\flightDefaultBlock;

use common\models\Lead;
use yii\rbac\Rule;

class LeadViewFlightDefaultBlockEmptyOwnerRule extends Rule
{
    public $name = 'LeadViewFlightDefaultBlockEmptyOwnerRule';

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
