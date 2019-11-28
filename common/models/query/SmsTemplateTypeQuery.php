<?php

namespace common\models\query;

use common\models\SmsTemplateType;

/**
 * This is the ActiveQuery class for [[SmsTemplateType]].
 *
 * @see SmsTemplateType
 */
class SmsTemplateTypeQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return SmsTemplateType[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SmsTemplateType|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
