<?php

namespace sales\rbac\rules\lead\splitRules;

use common\models\Lead;
use yii\rbac\Rule;

class SplitProfitLeadSoldRule extends Rule
{
    public $name = 'SplitProfitLeadSoldRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['lead']) || !$params['lead'] instanceof Lead) {
            return false;
        }

        /** @var Lead $lead */
        $lead = $params['lead'];
        return $lead->isSold();
    }
}
