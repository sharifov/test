<?php

namespace sales\rbac\rules\cases\take;

use sales\entities\cases\Cases;
use yii\rbac\Rule;

class CasesTakeOverRule extends Rule
{
    public $name = 'CasesTakeOverRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['case']) || !$params['case'] instanceof Cases) {
            return false;
        }

        /** @var Cases $case */
        $case = $params['case'];
        return $case->isProcessing() && !$case->isOwner($userId);
    }
}
