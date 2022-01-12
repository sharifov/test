<?php

namespace src\model\flightQuoteLabelList\entity;

/**
* @see FlightQuoteLabelList
*/
class FlightQuoteLabelListScopes extends \yii\db\ActiveQuery
{
    /**
    * @return FlightQuoteLabelList[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return FlightQuoteLabelList|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
