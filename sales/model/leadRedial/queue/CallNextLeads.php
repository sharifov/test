<?php

namespace sales\model\leadRedial\queue;

use common\models\Employee;
use common\models\search\LeadQcallSearch;
use yii\helpers\ArrayHelper;

class CallNextLeads implements Leads
{
    public function getLeads(Employee $user): array
    {
        $search = new LeadQcallSearch();
        $dataProvider = $search->searchByRedial([
            $search->formName() => [
                'l_is_test' => 0
            ]
        ], $user, false);
        $query = $dataProvider->query;
        $query->addOrderBy(($dataProvider->sort)->getOrders());
        $leads = $query->asArray()->all();
        return ArrayHelper::getColumn($leads, 'lqc_lead_id');
    }
}
