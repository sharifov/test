<?php

namespace sales\repositories\lead;

use common\models\LeadQcall;

class LeadQcallRepository
{

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
