<?php

namespace sales\rbac\rules\lead\splitRules;

use common\models\Lead;
use yii\rbac\Rule;

class SplitTipsLeadSoldRule extends Rule
{
    public $name = 'SplitTipsLeadSoldRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['lead']) || !$params['lead'] instanceof Lead) {
            return false;
        }

        /** @var Lead $lead */
        $lead = $params['lead'];
        return ($lead->isSold() && $lead->tips > 0);
    }
}
