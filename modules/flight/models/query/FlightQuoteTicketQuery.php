<?php

namespace modules\flight\models\query;

use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteBooking;
use modules\flight\models\FlightQuoteFlight;
use modules\flight\models\FlightQuoteTicket;

/**
* @see \modules\flight\models\FlightQuoteTicket
*/
class FlightQuoteTicketQuery extends \yii\db\ActiveQuery
{
    /**
    * @return \modules\flight\models\FlightQuoteTicket[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return \modules\flight\models\FlightQuoteTicket|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param int $id
     * @return FlightQuoteTicket[]
     */
    public static function findByFlightQuoteId(int $id): array
    {
        return FlightQuoteTicket::find()
            ->join('join', FlightQuoteBooking::tableName(), 'fqt_fqb_id = fqb_id')
            ->join('join', FlightQuoteFlight::tableName(), 'fqf_id = fqb_fqf_id')
            ->join('join', FlightQuote::tableName(), 'fq_id = fqf_fq_id')
            ->where(['fqf_fq_id' => $id])->all();
    }
}
