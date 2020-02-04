<?php

namespace modules\hotel\models\query;

/**
 * This is the ActiveQuery class for [[\modules\hotel\models\HotelQuoteServiceLog]].
 *
 * @see \modules\hotel\models\HotelQuoteServiceLog
 */
class HotelQuoteServiceLogQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \modules\hotel\models\HotelQuoteServiceLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \modules\hotel\models\HotelQuoteServiceLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
