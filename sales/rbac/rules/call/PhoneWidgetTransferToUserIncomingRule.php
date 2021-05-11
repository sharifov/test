<?php

namespace sales\rbac\rules\call;

use common\models\Call;
use yii\rbac\Rule;

class PhoneWidgetTransferToUserIncomingRule extends Rule
{
    public $name = 'PhoneWidgetTransferToUserIncomingRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['call']) || !$params['call'] instanceof Call) {
            return false;
        }

        return $params['call']->isIn();
    }
}
