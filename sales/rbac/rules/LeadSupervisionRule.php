<?php

namespace sales\rbac\rules;

use common\models\Lead;
use Yii;

class LeadSupervisionRule extends LeadRule
{
    public $name = 'isLeadSupervision';

    /**
     * @param int|string $userId
     * @param yii\rbac\Item $item
     * @param array $params
     * @return bool
     */
    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['leadId']) && !isset($params['lead'])) {
            throw new \InvalidArgumentException;
        }
        /** @var  Lead $params['lead'] */
        $leadId = $params['leadId'] ?? $params['lead']->id;
        $key = $this->name . '-' . $userId . '-' . $leadId;
        $can = Yii::$app->user->identity->getCache($key);
        if ($can === null) {
            try {
                $lead = $params['lead'] ?? $this->leadRepository->get($leadId);
                $bool = $lead->canAgentEdit($userId) || $lead->canSupervisionEdit(Yii::$app->user->identity->userGroupList);
                $can = Yii::$app->user->identity->setCache($key, $bool);
            } catch (\Throwable $e) {
                $can = false;
            }
        }
        return $can;
    }

}