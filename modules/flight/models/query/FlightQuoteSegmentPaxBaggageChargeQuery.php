<?php

namespace modules\flight\models\query;

/**
 * This is the ActiveQuery class for [[\modules\flight\models\FlightQuoteSegmentPaxBaggageCharge]].
 *
 * @see \modules\flight\models\FlightQuoteSegmentPaxBaggageCharge
 */
class FlightQuoteSegmentPaxBaggageChargeQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\FlightQuoteSegmentPaxBaggageCharge[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\FlightQuoteSegmentPaxBaggageCharge|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
