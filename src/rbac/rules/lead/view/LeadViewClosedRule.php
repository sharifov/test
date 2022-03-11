<?php

namespace src\rbac\rules\lead\view;

use common\models\Lead;
use yii\rbac\Rule;

class LeadViewClosedRule extends Rule
{
    /**
     * @inheritDoc
     */
    public function execute($user, $item, $params)
    {
        if (!isset($params['lead']) || !$params['lead'] instanceof Lead) {
            return false;
        }

        /** @var Lead $lead */
        $lead = $params['lead'];
        return $lead->isClosed();
    }
}
