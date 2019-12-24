<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[LeadQcall]].
 *
 * @see LeadQcall
 */
class LeadQcallQuery extends \yii\db\ActiveQuery
{
    /**
     * @param int $userId
     * @param int $leadId
     * @return bool
     */
    public function isUserReservedOtherLead(int $userId, int $leadId): bool
    {
        return $this
            ->andWhere(['lqc_reservation_user_id' => $userId])
            ->andWhere(['<>', 'lqc_lead_id', $leadId])
            ->andWhere(['IS NOT', 'lqc_reservation_time', null])
            ->andWhere(['>=', 'lqc_reservation_time', date('Y-m-d H:i:s')])
            ->exists();
    }

    public function currentReservedLeadByUser(int $userId): self
    {
        return $this
            ->andWhere(['lqc_reservation_user_id' => $userId])
            ->andWhere(['IS NOT', 'lqc_reservation_time', null])
            ->andWhere(['>=', 'lqc_reservation_time', date('Y-m-d H:i:s')]);
    }
}
