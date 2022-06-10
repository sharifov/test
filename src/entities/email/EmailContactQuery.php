<?php

namespace src\entities\email;

use yii\db\ActiveQuery;

class EmailContactQuery extends ActiveQuery
{
    public function isFrom()
    {
        return $this->andOnCondition(['ec_type_id' => EmailContact::TYPE_FROM]);
    }

    public function isTo()
    {
        return $this->andOnCondition(['ec_type_id' => EmailContact::TYPE_TO]);
    }

    public function isCc()
    {
        return $this->andOnCondition(['ec_type_id' => EmailContact::TYPE_CC]);
    }

    public function isBcc()
    {
        return $this->andOnCondition(['ec_type_id' => EmailContact::TYPE_BCC]);
    }
}
