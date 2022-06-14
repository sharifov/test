<?php

namespace modules\taskList\src\entities\taskList;

/**
 * This is the ActiveQuery class for [[TaskList]].
 *
 * @see TaskList
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TaskList[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TaskList|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
