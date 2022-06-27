<?php

namespace src\entities\email;

use yii\db\ActiveQuery;
use src\entities\email\helpers\EmailContactType;

class EmailContactQuery extends ActiveQuery
{
    public function from()
    {
        return $this->andOnCondition(['ec_type_id' => EmailContactType::FROM]);
    }

    public function to()
    {
        return $this->andOnCondition(['ec_type_id' => EmailContactType::TO]);
    }

    public function cc()
    {
        return $this->andOnCondition(['ec_type_id' => EmailContactType::CC]);
    }

    public function bcc()
    {
        return $this->andOnCondition(['ec_type_id' => EmailContactType::BCC]);
    }
}
