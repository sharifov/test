<?php

namespace sales\rbac\rules\email\view;

use common\models\Email;
use sales\access\EmployeeGroupAccess;
use yii\rbac\Rule;

class EmailViewGroupRule extends Rule
{
    public $name = 'EmailViewGroupRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['email']) || !$params['email'] instanceof Email) {
            return false;
        }

        /** @var Email $email */
        $email = $params['email'];

        if (!$email->hasCreatedUser()) {
            return false;
        }

        return EmployeeGroupAccess::isUserInCommonGroup((int)$userId, (int)$email->e_created_user_id);
    }
}
