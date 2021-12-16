<?php

namespace modules\flight\src\entities\flightQuoteTicketRefund;

/**
 * This is the ActiveQuery class for [[FlightQuoteTicketRefund]].
 *
 * @see FlightQuoteTicketRefund
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return FlightQuoteTicketRefund[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return FlightQuoteTicketRefund|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
