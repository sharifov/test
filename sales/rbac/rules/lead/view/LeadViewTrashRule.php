<?php

namespace sales\rbac\rules\lead\view;

use common\models\Lead;
use yii\rbac\Rule;

class LeadViewTrashRule extends Rule
{
    public $name = 'LeadViewTrashRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['lead']) || !$params['lead'] instanceof Lead) {
            return false;
        }

        /** @var Lead $lead */
        $lead = $params['lead'];
        return $lead->isTrash();
    }
}
