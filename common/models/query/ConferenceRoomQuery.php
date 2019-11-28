<?php

namespace common\models\query;

use common\models\ConferenceRoom;

/**
 * This is the ActiveQuery class for [[ConferenceRoom]].
 *
 * @see ConferenceRoom
 */
class ConferenceRoomQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ConferenceRoom[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ConferenceRoom|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
