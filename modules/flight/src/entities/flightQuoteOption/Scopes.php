<?php

namespace modules\flight\src\entities\flightQuoteOption;

/**
* @see FlightQuoteOption
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return FlightQuoteOption[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return FlightQuoteOption|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
