<?php

namespace sales\rbac\rules\lead\view\files;

use common\models\Lead;
use yii\rbac\Rule;

class LeadViewFilesViewEmptyOwnerRule extends Rule
{
    public $name = 'LeadViewFilesViewEmptyOwnerRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['lead']) || !$params['lead'] instanceof Lead) {
            return false;
        }

        $lead = $params['lead'];

        return !$lead->hasOwner();
    }
}
