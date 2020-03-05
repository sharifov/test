<?php

namespace sales\rbac\rules\lead\view;

use common\models\Lead;
use yii\rbac\Rule;

/**
 * Class LeadViewHybridUidByStatusRule
 */
class LeadViewHybridUidByStatusRule extends Rule
{
    public $name = 'lead/view_HybridUid_ViewByStatusRule';

    /**
     * @param int|string $userId
     * @param \yii\rbac\Item $item
     * @param array $params
     * @return bool
     */
    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['lead']) || !$params['lead'] instanceof Lead) {
            return false;
        }
        /** @var Lead $lead */
        $lead = $params['lead'];
        return ($lead->isSold() || $lead->isBooked() || $lead->isReject());
    }
}
