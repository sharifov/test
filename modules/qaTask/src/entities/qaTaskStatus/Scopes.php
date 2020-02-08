<?php

namespace modules\qaTask\src\entities\qaTaskStatus;

/**
 * This is the ActiveQuery class for [[QaTaskStatus]].
 *
 * @see QaTaskStatus
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return QaTaskStatus[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return QaTaskStatus|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
