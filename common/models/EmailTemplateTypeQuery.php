<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[EmailTemplateType]].
 *
 * @see EmailTemplateType
 */
class EmailTemplateTypeQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return EmailTemplateType[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return EmailTemplateType|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
