<?php

namespace sales\rbac\rules;

use common\models\Lead;
use yii\rbac\Rule;

/**
 * Class LeadSearchMultipleUpdateSupervisionRule
 */
class LeadSearchMultipleUpdateSupervisionRule extends Rule
{

    public $name = 'LeadSearchMultipleUpdateSupervisionRule';

    /**
     * @param int|string $user
     * @param yii\rbac\Item $item
     * @param array $params
     * @return bool
     */
    public function execute($user, $item, $params): bool
    {
        if (!isset($params['lead']) || !$params['lead'] instanceof Lead) {
            return false;
        }
        /** @var Lead $lead */
        $lead = $params['lead'];
        return ($lead->isProcessing() || $lead->isFollowUp() || $lead->isOnHold() || $lead->isTrash() || $lead->isSnooze());
    }
}