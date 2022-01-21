<?php

namespace src\rbac\rules\cases\update;

use src\entities\cases\Cases;
use yii\rbac\Rule;

class CasesUpdateActiveOwnRule extends Rule
{
    public $name = 'CasesUpdateActiveOwnRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['case']) || !$params['case'] instanceof Cases) {
            return false;
        }

        /** @var Cases $case */
        $case = $params['case'];
        return $case->isActive() && $case->isOwner($userId);
    }
}
