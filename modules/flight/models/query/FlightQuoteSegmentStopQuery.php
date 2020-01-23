<?php

namespace modules\flight\models\query;

/**
 * This is the ActiveQuery class for [[\modules\flight\models\FlightQuoteSegmentStop]].
 *
 * @see \modules\flight\models\FlightQuoteSegmentStop
 */
class FlightQuoteSegmentStopQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\FlightQuoteSegmentStop[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\FlightQuoteSegmentStop|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
