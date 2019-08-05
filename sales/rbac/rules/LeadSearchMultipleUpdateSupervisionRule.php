<?php

namespace sales\rbac\rules;

use common\models\Lead;
use yii\helpers\VarDumper;
use yii\rbac\Rule;

class LeadSearchMultipleSelectSupervisionRule extends Rule
{

    public $name = 'LeadSearchMultipleSelectSupervisionRule';

    public function execute($user, $item, $params)
    {
        if (!isset($params['lead']) || !$params['lead'] instanceof Lead) {
            return false;
        }
        /** @var Lead $lead */
        $lead = $params['lead'];
        if ($lead->isProcessing() || $lead->isFollowUp()) {
            return true;
        }
        return false;
    }
}