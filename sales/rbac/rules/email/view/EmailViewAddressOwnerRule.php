<?php

namespace sales\rbac\rules\email\view;

use common\models\Email;
use common\models\UserProjectParams;
use yii\rbac\Rule;

class EmailViewAddressOwnerRule extends Rule
{
    public $name = 'EmailViewAddressOwnerRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['email']) || !$params['email'] instanceof Email) {
            return false;
        }

        /** @var Email $email */
        $email = $params['email'];

        return UserProjectParams::find()
            ->byUserId((int)$userId)
            ->byEmail([$email->e_email_from, $email->e_email_to], false)
            ->exists();
    }
}
