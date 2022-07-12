<?php

namespace src\model\clientReturnIndication\entity;

/**
 * This is the ActiveQuery class for [[ClientReturnIndication]].
 *
 * @see ClientReturnIndication
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ClientReturnIndication[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ClientReturnIndication|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
