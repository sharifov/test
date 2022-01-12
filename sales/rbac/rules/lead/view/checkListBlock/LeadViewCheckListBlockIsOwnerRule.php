<?php

namespace sales\rbac\rules\lead\view\checkListBlock;

use common\models\Lead;
use yii\rbac\Rule;

class LeadViewCheckListBlockIsOwnerRule extends Rule
{
    public $name = 'LeadViewCheckListBlockIsOwnerRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['lead']) || !$params['lead'] instanceof Lead) {
            return false;
        }

        /** @var Lead $lead */
        $lead = $params['lead'];

        return $lead->isOwner($userId);
    }
}
