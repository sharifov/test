<?php

namespace modules\flight\src\entities\flightQuoteLabel;

/**
* @see FlightQuoteLabel
*/
class FlightQuoteLabelScopes extends \yii\db\ActiveQuery
{
    /**
    * @return FlightQuoteLabel[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return FlightQuoteLabel|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
