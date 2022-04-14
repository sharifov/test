<?php

namespace common\models\query;

use common\models\EmailTemplateType;

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

    /**
     * @param string $key
     * @return EmailTemplateTypeQuery
     */
    public function findByTemplateKey(string $key): EmailTemplateTypeQuery
    {
        return $this->andWhere(['etp_key' => $key])
            ->limit(1);
    }
}
