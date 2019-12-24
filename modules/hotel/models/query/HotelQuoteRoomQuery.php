<?php

namespace modules\hotel\models\query;

/**
 * This is the ActiveQuery class for [[\modules\hotel\models\HotelQuoteRoom]].
 *
 * @see \modules\hotel\models\HotelQuoteRoom
 */
class HotelQuoteRoomQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \modules\hotel\models\HotelQuoteRoom[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \modules\hotel\models\HotelQuoteRoom|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
