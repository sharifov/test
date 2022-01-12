<?php

namespace sales\rbac\rules\email\view;

use common\models\Email;
use yii\rbac\Rule;

class EmailViewEmptyRule extends Rule
{
    public $name = 'EmailViewEmptyRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['email']) || !$params['email'] instanceof Email) {
            return false;
        }

        /** @var Email $email */
        $email = $params['email'];

        return !$email->hasCreatedUser();
    }
}
