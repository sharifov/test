<?php

namespace src\rbac\rules\lead\view\files;

use common\models\Lead;
use yii\rbac\Rule;

class LeadViewFilesViewIsOwnerRule extends Rule
{
    public $name = 'LeadViewFilesViewIsOwnerRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['lead']) || !$params['lead'] instanceof Lead) {
            return false;
        }

        $lead = $params['lead'];

        if (!$lead->hasOwner()) {
            return false;
        }

        return $lead->isOwner($userId);
    }
}
