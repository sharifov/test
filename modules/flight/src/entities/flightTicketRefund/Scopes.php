<?php

namespace modules\flight\src\entities\flightTicketRefund;

/**
 * This is the ActiveQuery class for [[FlightTicketRefund]].
 *
 * @see FlightTicketRefund
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return FlightTicketRefund[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return FlightTicketRefund|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
