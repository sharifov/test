<?php

namespace frontend\widgets\client;

use common\models\Lead;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use yii\base\Widget;
use yii\db\Query;

/**
 * Class ClientCounterWidget
 *
 * @property int $clientId
 */
class ClientCounterWidget extends Widget
{
    public $clientId;

    /**
     * @return string|null
     */
    public function run(): ?string
    {
        if (!$this->clientId) {
            return null;
        }

        return $this->render('client_counter', [
            'allLeads' => $this->countAllLeads(),
            'activeLeads' => $this->countActiveLeads(),
            'allCases' => $this->countAllCases(),
            'activeCases' => $this->countActiveCases()
        ]);
    }

    /**
     * @return int
     */
    private function countActiveLeads(): int
    {
        return (new Query)->select(['client_id', 'status'])->from(Lead::tableName())
            ->andWhere(['client_id' => $this->clientId])
            ->andWhere(['NOT IN', 'status', [Lead::STATUS_TRASH, Lead::STATUS_SOLD, Lead::STATUS_REJECT]])
            ->count();
    }

    /**
     * @return int
     */
    private function countAllLeads(): int
    {
        return (new Query)->select(['client_id', 'status'])->from(Lead::tableName())
            ->andWhere(['client_id' => $this->clientId])
            ->count();
    }

    /**
     * @return int
     */
    private function countActiveCases(): int
    {
        return (new Query)->select(['cs_client_id', 'cs_status'])->from(Cases::tableName())
            ->andWhere(['cs_client_id' => $this->clientId])
            ->andWhere(['NOT IN', 'cs_status', [CasesStatus::STATUS_SOLVED, CasesStatus::STATUS_TRASH]])
            ->count();
    }

    /**
     * @return int
     */
    private function countAllCases(): int
    {
        return (new Query)->select(['cs_client_id', 'cs_status'])->from(Cases::tableName())
            ->andWhere(['cs_client_id' => $this->clientId])
            ->count();
    }

}
