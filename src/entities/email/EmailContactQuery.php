<?php

namespace src\entities\email;

use yii\db\ActiveQuery;
use src\entities\email\helpers\EmailContactType;

class EmailContactQuery extends ActiveQuery
{
    public function isFrom(): EmailContactQuery
    {
        return $this->andOnCondition(['ec_type_id' => EmailContactType::FROM]);
    }

    public function isTo(): EmailContactQuery
    {
        return $this->andOnCondition(['ec_type_id' => EmailContactType::TO]);
    }

    public function isCc(): EmailContactQuery
    {
        return $this->andOnCondition(['ec_type_id' => EmailContactType::CC]);
    }

    public function isBcc(): EmailContactQuery
    {
        return $this->andOnCondition(['ec_type_id' => EmailContactType::BCC]);
    }

    public function byType(int $type): EmailContactQuery
    {
        return $this->andOnCondition(['ec_type_id' => $type]);
    }

    public function byEmail(int $emailId): EmailContactQuery
    {
        return $this->andWhere(['ec_email_id' => $emailId]);
    }
}
