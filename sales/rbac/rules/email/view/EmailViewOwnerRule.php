<?php

namespace sales\rbac\rules\email\view;

use common\models\Email;
use yii\rbac\Rule;

class EmailViewOwnerRule extends Rule
{
    public $name = 'EmailViewOwnerRule';

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

        return $email->isCreatedUser((int)$userId);
    }
}
