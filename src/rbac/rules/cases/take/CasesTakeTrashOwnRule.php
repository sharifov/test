<?php

namespace src\rbac\rules\cases\take;

use src\entities\cases\Cases;
use yii\rbac\Rule;

class CasesTakeTrashOwnRule extends Rule
{
    public $name = 'CasesTakeTrashOwnRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['case']) || !$params['case'] instanceof Cases) {
            return false;
        }

        /** @var Cases $case */
        $case = $params['case'];
        return $case->isTrash() && $case->isOwner($userId);
    }
}
