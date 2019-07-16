<?php

namespace sales\repositories\lead;

use common\models\LeadChecklist;

class LeadChecklistRepository
{

    /**
     * @param $userId
     * @param $leadId
     * @return array|LeadChecklist[]
     */
    public function get($userId, $leadId): array
    {
        return LeadChecklist::find()->andWhere(['lc_user_id' => $userId, 'lc_lead_id' => $leadId])->orderBy(['lc_created_dt' => SORT_ASC])->all();
    }

    /**
     * @param LeadChecklist $leadChecklist
     */
    public function save(LeadChecklist $leadChecklist): void
    {
        if (!$leadChecklist->save(false)) {
            throw new \RuntimeException('Saving error');
        }
    }

    /**
     * @param LeadChecklist $leadChecklist
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(LeadChecklist $leadChecklist): void
    {
        if (!$leadChecklist->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }

}
