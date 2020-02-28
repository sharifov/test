<?php

namespace common\models\query;

use common\models\VisitorLog;

/**
 * This is the ActiveQuery class for [[VisitorLog]].
 *
 * @see VisitorLog
 */
class VisitorLogQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return VisitorLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return VisitorLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
