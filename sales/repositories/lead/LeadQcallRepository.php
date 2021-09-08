<?php

namespace sales\repositories\lead;

use common\models\LeadQcall;
use sales\repositories\NotFoundException;

class LeadQcallRepository
{
    public function find(int $leadId): LeadQcall
    {
        $leadQcall = LeadQcall::find()->byLeadId($leadId)->one();
        if ($leadQcall) {
            return $leadQcall;
        }
        throw new NotFoundException('LeadQcall not found. LeadId: ' . $leadId);
    }

    /**
     * @param LeadQcall $qCall
     * @return int
     */
    public function save(LeadQcall $qCall): int
    {
        if (!$qCall->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        return $qCall->lqc_lead_id;
    }

    /**
     * @param LeadQcall $qCall
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(LeadQcall $qCall): void
    {
        if (!$qCall->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }
}
