<?php

namespace src\rbac\rules\email\view;

use common\models\Email;
use yii\rbac\Rule;

class EmailViewLeadOwnerRule extends Rule
{
    public $name = 'EmailViewLeadOwnerRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['email']) || !$params['email'] instanceof Email) {
            return false;
        }

        /** @var Email $email */
        $email = $params['email'];

        if (!$email->e_lead_id) {
            return false;
        }

        return $email->lead->isOwner((int)$userId);
    }
}
