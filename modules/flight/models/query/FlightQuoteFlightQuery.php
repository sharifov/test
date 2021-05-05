<?php

namespace modules\flight\models\query;

/**
* @see \modules\flight\models\FlightQuoteFlight
*/
class FlightQuoteFlightQuery extends \yii\db\ActiveQuery
{
    /**
    * @return \modules\flight\models\FlightQuoteFlight[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return \modules\flight\models\FlightQuoteFlight|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
