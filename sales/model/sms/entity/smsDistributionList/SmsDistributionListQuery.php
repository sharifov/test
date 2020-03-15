<?php

namespace sales\model\sms\entity\smsDistributionList;

/**
 * This is the ActiveQuery class for [[SmsDistributionList]].
 *
 * @see SmsDistributionList
 */
class SmsDistributionListQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return SmsDistributionList[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SmsDistributionList|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
