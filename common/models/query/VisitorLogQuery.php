<?php

namespace common\models\query;

use common\models\VisitorLog;

/**
 * @see VisitorLog
 */
class VisitorLogQuery extends \yii\db\ActiveQuery
{
    public function limitFields(): self
    {
        return $this->select([
            'vl_source_cid', 'vl_ga_client_id', 'vl_ga_user_id', 'vl_customer_id',
            'vl_gclid', 'vl_dclid', 'vl_utm_source', 'vl_utm_medium', 'vl_utm_campaign', 'vl_utm_term',
            'vl_utm_content', 'vl_referral_url', 'vl_location_url', 'vl_user_agent', 'vl_ip_address', 'vl_visit_dt',
            'vl_created_dt'
        ]);
    }

    public function byLead(int $leadId): self
    {
        return $this->andWhere(['vl_lead_id' => $leadId]);
    }

    public function byId(int $id): self
    {
        return $this->andWhere(['vl_id' => $id]);
    }
}
