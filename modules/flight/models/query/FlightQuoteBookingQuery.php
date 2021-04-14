<?php

namespace modules\flight\models\query;

/**
* @see \modules\flight\models\FlightQuoteBooking
*/
class FlightQuoteBookingQuery extends \yii\db\ActiveQuery
{
    /**
    * @return \modules\flight\models\FlightQuoteBooking[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return \modules\flight\models\FlightQuoteBooking|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
