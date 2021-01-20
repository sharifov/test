<?php

namespace sales\rbac\rules\cases\view\files;

use sales\entities\cases\Cases;
use yii\rbac\Rule;

class CaseViewFilesViewEmptyOwnerRule extends Rule
{
    public $name = 'CaseViewFilesViewEmptyOwnerRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['case']) || !$params['case'] instanceof Cases) {
            return false;
        }

        $case = $params['case'];

        return $case->isFreedOwner();
    }
}
