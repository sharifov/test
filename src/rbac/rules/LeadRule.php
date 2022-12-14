<?php

namespace src\rbac\rules;

use common\models\Lead;
use src\repositories\lead\LeadRepository;
use Yii;
use yii\rbac\Rule;

abstract class LeadRule extends Rule
{
    /**
     * @param int|string $userId
     * @param yii\rbac\Item $item
     * @param array $params
     * @return bool
     */
    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['leadId']) && !isset($params['lead'])) {
            throw new \InvalidArgumentException('leadId or lead must be set');
        }
        $leadRepository = Yii::createObject(LeadRepository::class);
        /** @var  Lead $params['lead'] */
        $leadId = $params['leadId'] ?? $params['lead']->id;
        $leadId = (int)$leadId;
        $key = $this->name . '-' . $userId . '-' . $leadId;
        $can = Yii::$app->user->identity->getCache($key);
        if ($can === null) {
            try {
                $lead = $params['lead'] ?? $leadRepository->find($leadId);
                $data = $this->getData($userId, $lead);
                $can = Yii::$app->user->identity->setCache($key, $data);
            } catch (\Throwable $e) {
                $can = false;
            }
        }
        return $can;
    }

    abstract public function getData(int $userId, Lead $lead);
}
