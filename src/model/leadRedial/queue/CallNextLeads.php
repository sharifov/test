<?php

namespace src\model\leadRedial\queue;

use common\models\Employee;
use common\models\search\LeadQcallSearch;
use yii\helpers\ArrayHelper;

class CallNextLeads implements Leads
{
    public function getLeads(Employee $user): array
    {
        $leads = (new LeadQcallSearch())
            ->searchRedialLeadByUser($user, new \DateTimeImmutable())
            ->asArray()
            ->all();
        return ArrayHelper::getColumn($leads, 'lqc_lead_id');
    }
}
