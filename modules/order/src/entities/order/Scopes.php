<?php

namespace modules\order\src\entities\order;

use sales\model\caseOrder\entity\CaseOrder;
use sales\model\leadOrder\entity\LeadOrder;
use yii\db\Expression;

/**
 * @see Order
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function byGid(string $gid): Scopes
    {
        return $this->andWhere(['or_gid' => $gid]);
    }

    public function joinLeadOrdersByLead(int $leadId): Scopes
    {
        return $this->join('join', LeadOrder::tableName(), new Expression(
            'lo_order_id = or_id and lo_lead_id = :leadId',
            ['leadId' => $leadId]
        ));
    }

    public function joinCaseOrdersByCase(int $caseId): Scopes
    {
        return $this->join('join', CaseOrder::tableName(), new Expression(
            'co_order_id = or_id and co_case_id = :caseId',
            ['caseId' => $caseId]
        ));
    }
}
