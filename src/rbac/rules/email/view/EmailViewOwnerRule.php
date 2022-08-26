<?php

namespace src\rbac\rules\email\view;

use common\models\Email;
use yii\rbac\Rule;
use src\entities\email\EmailInterface;

class EmailViewOwnerRule extends Rule
{
    public $name = 'EmailViewOwnerRule';

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

        return $email->isCreatedUser((int)$userId);
    }
}
