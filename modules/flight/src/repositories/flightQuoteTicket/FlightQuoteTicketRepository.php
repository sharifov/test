<?php

namespace modules\flight\src\repositories\flightQuoteTicket;

use modules\flight\models\FlightQuoteTicket;

/**
 * Class FlightQuoteTicketRepository
 */
class FlightQuoteTicketRepository
{
    public function save(FlightQuoteTicket $model): void
    {
        if (!$model->save(false)) {
            throw new \RuntimeException('FlightQuoteTicket save is failed');
        }
    }
}
