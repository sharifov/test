<?php

namespace sales\rbac\rules\cases\view\files;

use sales\entities\cases\Cases;
use yii\rbac\Rule;

class CaseViewFilesViewIsOwnerRule extends Rule
{
    public $name = 'CaseViewFilesViewIsOwnerRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['case']) || !$params['case'] instanceof Cases) {
            return false;
        }

        $case = $params['case'];

        if ($case->isFreedOwner()) {
            return false;
        }

        return $case->isOwner($userId);
    }
}
