<?php

namespace src\rbac\rules\email\view;

use common\models\UserProjectParams;
use yii\rbac\Rule;
use src\entities\email\EmailInterface;

class EmailViewAddressOwnerRule extends Rule
{
    public $name = 'EmailViewAddressOwnerRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['email']) || !$params['email'] instanceof EmailInterface) {
            return false;
        }

        /** @var EmailInterface $email */
        $email = $params['email'];

        return UserProjectParams::find()
            ->byUserId((int)$userId)
            ->byEmail([$email->getEmailFrom(false), $email->getEmailTo(false)], false)
            ->exists();
    }
}
