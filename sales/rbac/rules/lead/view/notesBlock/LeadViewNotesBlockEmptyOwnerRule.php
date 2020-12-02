<?php

namespace sales\rbac\rules\lead\view\notesBlock;

use common\models\Lead;
use yii\rbac\Rule;

class LeadViewNotesBlockEmptyOwnerRule extends Rule
{
    public $name = 'LeadViewNotesBlockEmptyOwnerRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['lead']) || !$params['lead'] instanceof Lead) {
            return false;
        }

        /** @var Lead $lead */
        $lead = $params['lead'];

        return !$lead->hasOwner();
    }
}
