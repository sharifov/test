<?php

namespace modules\flight\models\query;

/**
* @see \modules\flight\models\FlightQuoteBookingAirline
*/
class FlightQuoteBookingAirlineQuery extends \yii\db\ActiveQuery
{
    /**
    * @return \modules\flight\models\FlightQuoteBookingAirline[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return \modules\flight\models\FlightQuoteBookingAirline|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
