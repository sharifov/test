<?php

namespace sales\rbac\rules\cases\view;

use sales\entities\cases\Cases;
use yii\rbac\Rule;

class CasesViewCheckedOwnerRule extends Rule
{
    public $name = 'CasesViewCheckedOwnerRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['case']) || !$params['case'] instanceof Cases) {
            return false;
        }

        /** @var Cases $case */
        $case = $params['case'];

        return $case->isOwner((int)$userId) || $case->isTrash() || $case->isSolved();
    }
}
