<?php

namespace modules\rentCar\src\entity\rentCarQuote;

/**
* @see RentCarQuote
*/
class RentCarQuoteScopes extends \yii\db\ActiveQuery
{
    /**
    * @return RentCarQuote[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return RentCarQuote|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
