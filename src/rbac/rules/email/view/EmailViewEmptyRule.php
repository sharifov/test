<?php

namespace src\rbac\rules\email\view;

use yii\rbac\Rule;
use src\entities\email\EmailInterface;

class EmailViewEmptyRule extends Rule
{
    public $name = 'EmailViewEmptyRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['email']) || !$params['email'] instanceof EmailInterface) {
            return false;
        }

        /** @var EmailInterface $email */
        $email = $params['email'];

        return !$email->hasCreatedUser();
    }
}
