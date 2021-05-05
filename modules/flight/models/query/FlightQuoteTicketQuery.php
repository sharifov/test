<?php

namespace modules\flight\models\query;

/**
* @see \modules\flight\models\FlightQuoteTicket
*/
class FlightQuoteTicketQuery extends \yii\db\ActiveQuery
{
    /**
    * @return \modules\flight\models\FlightQuoteTicket[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return \modules\flight\models\FlightQuoteTicket|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
