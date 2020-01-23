<?php

namespace modules\flight\models\query;

/**
 * This is the ActiveQuery class for [[\modules\flight\models\FlightQuoteTrip]].
 *
 * @see \modules\flight\models\FlightQuoteTrip
 */
class FlightQuoteTripQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\FlightQuoteTrip[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\FlightQuoteTrip|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
