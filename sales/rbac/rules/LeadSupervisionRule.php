<?php

namespace sales\rbac\rules;

use common\models\Lead;
use Yii;

class LeadSupervisionRule extends LeadRule
{
    public $name = 'isLeadSupervision';

    public function getData(int $userId, Lead $lead): bool
    {
        return $lead->canAgentEdit($userId) || $lead->canSupervisionEdit(Yii::$app->user->identity->userGroupList);
    }

}