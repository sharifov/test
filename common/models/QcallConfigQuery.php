<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[QcallConfig]].
 *
 * @see QcallConfig
 */
class QcallConfigQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return QcallConfig[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return QcallConfig|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
