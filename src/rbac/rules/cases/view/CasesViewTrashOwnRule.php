<?php

namespace src\rbac\rules\cases\view;

use src\entities\cases\Cases;
use yii\rbac\Rule;

class CasesViewTrashOwnRule extends Rule
{
    public $name = 'CasesViewTrashOwnRule';

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
