<?php

namespace modules\flight\models\query;

/**
 * This is the ActiveQuery class for [[\modules\flight\models\FlightQuoteStatusLog]].
 *
 * @see \modules\flight\models\FlightQuoteStatusLog
 */
class FlightQuoteStatusLogQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\FlightQuoteStatusLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\FlightQuoteStatusLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
