<?php

namespace src\rbac\rules\lead\view;

use common\models\Lead;
use yii\rbac\Rule;

class LeadViewProcessingRule extends Rule
{
    public $name = 'LeadViewProcessingRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['lead']) || !$params['lead'] instanceof Lead) {
            return false;
        }

        /** @var Lead $lead */
        $lead = $params['lead'];
        return $lead->isProcessing();
    }
}
