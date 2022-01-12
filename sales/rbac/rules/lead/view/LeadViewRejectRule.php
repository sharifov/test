<?php

namespace sales\rbac\rules\lead\view;

use common\models\Lead;
use yii\rbac\Rule;

class LeadViewRejectRule extends Rule
{
    public $name = 'LeadViewRejectRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['lead']) || !$params['lead'] instanceof Lead) {
            return false;
        }

        /** @var Lead $lead */
        $lead = $params['lead'];
        return $lead->isReject();
    }
}
