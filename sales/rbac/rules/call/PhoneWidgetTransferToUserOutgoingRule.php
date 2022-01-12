<?php

namespace sales\rbac\rules\call;

use common\models\Call;
use yii\rbac\Rule;

class PhoneWidgetTransferToUserOutgoingRule extends Rule
{
    public $name = 'PhoneWidgetTransferToUserOutgoingRule';

    public function execute($userId, $item, $params): bool
    {
        if (!isset($params['call']) || !$params['call'] instanceof Call) {
            return false;
        }

        return $params['call']->isOut();
    }
}
