<?php

namespace sales\rbac\rules\cases\take;

use sales\entities\cases\Cases;
use yii\rbac\Rule;

class CasesTakeFollowUpRule extends Rule
{
    public $name = 'CasesTakeFollowUpRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['case']) || !$params['case'] instanceof Cases) {
            return false;
        }

        /** @var Cases $case */
        $case = $params['case'];
        return $case->isFollowUp() && !$case->isOwner($userId);
    }
}
