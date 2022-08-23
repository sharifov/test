<?php

namespace src\rbac\rules\email\view;

use yii\rbac\Rule;
use src\entities\email\EmailInterface;

class EmailViewCaseOwnerRule extends Rule
{
    public $name = 'EmailViewCaseOwnerRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['email']) || !$params['email'] instanceof EmailInterface) {
            return false;
        }

        /** @var Email $email */
        $email = $params['email'];

        if (!$email->hasCase()) {
            return false;
        }

        return $email->case->isOwner((int)$userId);
    }
}
