<?php

namespace common\models\query;

use common\models\Sms;

/**
 * This is the ActiveQuery class for [[Sms]].
 *
 * @see Sms
 */
class SmsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Sms[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Sms|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
