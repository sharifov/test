<?php

namespace src\entities\email;

use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

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
