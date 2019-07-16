<?php

namespace sales\repositories\lead;

use common\models\LeadFlowChecklist;
use sales\repositories\NotFoundException;

class LeadFlowChecklistRepository
{

    /**
     * @param $id
     * @return LeadFlowChecklist
     */
    public function get($id): LeadFlowChecklist
    {
        if ($leadFlowChecklist = LeadFlowChecklist::findOne($id)) {
            return $leadFlowChecklist;
        }
        throw new NotFoundException('LeadFlowChecklist is not found');
    }

    /**
     * @param LeadFlowChecklist $leadFlowChecklist
     */
    public function save(LeadFlowChecklist $leadFlowChecklist): void
    {
        if (!$leadFlowChecklist->save(false)) {
            throw new \RuntimeException('Saving error');
        }
    }

    /**
     * @param LeadFlowChecklist $leadFlowChecklist
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(LeadFlowChecklist $leadFlowChecklist): void
    {
        if (!$leadFlowChecklist->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }

}
