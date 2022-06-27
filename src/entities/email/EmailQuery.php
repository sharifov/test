<?php

namespace src\entities\email;

use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use src\entities\email\helpers\EmailType;

class EmailQuery extends ActiveQuery
{
    public function notDeleted()
    {
        return $this->andWhere(['e_is_deleted' => false]);
    }

    public function deleted()
    {
        return $this->andWhere(['e_is_deleted' => true]);
    }

    public function inbox()
    {
        return $this->andWhere(['e_type_id' => EmailType::INBOX]);
    }

    public function outbox()
    {
        return $this->andWhere(['e_type_id' => EmailType::OUTBOX]);
    }

    public function draft()
    {
        return $this->andWhere(['e_type_id' => EmailType::DRAFT]);
    }

    public function unread()
    {
        return $this
            ->join('LEFT JOIN', ['el' => EmailLog::tableName()], 'el.el_email_id = e_id')
            ->andWhere(['el_is_new' => true]);
    }

    public function withContact(array $mailList)
    {
        $addresses = EmailAddress::find()
                ->select('ea_id')
                ->where(['ea_email' => $mailList])
                ->asArray()
                ->all();

        $address_ids = ArrayHelper::getColumn($addresses, 'ea_id');

        return $this
            ->join('LEFT JOIN', ['ec' => EmailContact::tableName()], 'ec.ec_email_id = e_id')
            ->andWhere(['ec.ec_address_id' => $address_ids]);
    }
}
