<?php

namespace modules\hotel\models\query;

/**
 * This is the ActiveQuery class for [[\modules\hotel\models\HotelQuote]].
 *
 * @see \modules\hotel\models\HotelQuote
 */
class HotelQuoteQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \modules\hotel\models\HotelQuote[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \modules\hotel\models\HotelQuote|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
