<?php

namespace src\rbac\rules\email\view;

use common\models\Email;
use src\access\EmployeeGroupAccess;
use yii\rbac\Rule;
use src\entities\email\EmailInterface;

class EmailViewGroupRule extends Rule
{
    public $name = 'EmailViewGroupRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['email']) || !$params['email'] instanceof EmailInterface) {
            return false;
        }

        /** @var EmailInterface $email */
        $email = $params['email'];

        if (!$email->hasCreatedUser()) {
            return false;
        }

        return EmployeeGroupAccess::isUserInCommonGroup((int)$userId, (int)$email->e_created_user_id);
    }
}
