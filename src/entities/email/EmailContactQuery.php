<?php

namespace src\entities\email;

use yii\db\ActiveQuery;
use src\entities\email\helpers\EmailContactType;

class EmailContactQuery extends ActiveQuery
{
    public function isFrom()
    {
        return $this->andOnCondition(['ec_type_id' => EmailContactType::FROM]);
    }

    public function isTo()
    {
        return $this->andOnCondition(['ec_type_id' => EmailContactType::TO]);
    }

    public function isCc()
    {
        return $this->andOnCondition(['ec_type_id' => EmailContactType::CC]);
    }

    public function isBcc()
    {
        return $this->andOnCondition(['ec_type_id' => EmailContactType::BCC]);
    }
}
