<?php

namespace src\rbac\rules\email\view;

use common\models\Email;
use yii\rbac\Rule;

class EmailViewCaseOwnerRule extends Rule
{
    public $name = 'EmailViewCaseOwnerRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['email']) || !$params['email'] instanceof Email) {
            return false;
        }

        /** @var Email $email */
        $email = $params['email'];

        if (!$email->e_case_id) {
            return false;
        }

        return $email->case->isOwner((int)$userId);
    }
}
