<?php

namespace modules\flight\models\query;

/**
 * This is the ActiveQuery class for [[\modules\flight\models\FlightQuotePaxPrice]].
 *
 * @see \modules\flight\models\FlightQuotePaxPrice
 */
class FlightQuotePaxPriceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\FlightQuotePaxPrice[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\FlightQuotePaxPrice|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
