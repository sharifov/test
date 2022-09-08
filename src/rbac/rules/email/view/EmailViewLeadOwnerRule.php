<?php

namespace src\rbac\rules\email\view;

use common\models\Email;
use yii\rbac\Rule;
use src\entities\email\EmailInterface;

class EmailViewLeadOwnerRule extends Rule
{
    public $name = 'EmailViewLeadOwnerRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['email']) || !$params['email'] instanceof EmailInterface) {
            return false;
        }

        /** @var Email $email */
        $email = $params['email'];

        if (!$email->hasLead()) {
            return false;
        }

        return $email->lead->isOwner((int)$userId);
    }
}
