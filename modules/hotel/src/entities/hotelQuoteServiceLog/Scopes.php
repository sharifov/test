<?php

namespace modules\hotel\src\entities\hotelQuoteServiceLog;

/**
 * This is the ActiveQuery class for [[\modules\hotel\src\entities\hotelQuoteServiceLog]].
 *
 * @see \modules\hotel\models\HotelQuoteServiceLog
 */
class Scopes extends \yii\db\ActiveQuery
{

    /**
     * @param null $db
     * @return array|\yii\db\ActiveRecord[]
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @param null $db
     * @return array|\yii\db\ActiveRecord|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
