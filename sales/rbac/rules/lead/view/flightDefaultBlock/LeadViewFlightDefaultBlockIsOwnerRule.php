<?php

namespace sales\rbac\rules\lead\view\flightDefaultBlock;

use common\models\Lead;
use yii\rbac\Rule;

class LeadViewFlightDefaultBlockIsOwnerRule extends Rule
{
    public $name = 'LeadViewFlightDefaultBlockIsOwnerRule';

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
