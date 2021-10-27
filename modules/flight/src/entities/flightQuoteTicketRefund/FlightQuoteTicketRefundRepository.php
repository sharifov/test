<?php

namespace modules\flight\src\entities\flightQuoteTicketRefund;

class FlightQuoteTicketRefundRepository
{
    public function save(FlightQuoteTicketRefund $flightQuoteTicketRefund): int
    {
        if (!$flightQuoteTicketRefund->save()) {
            throw new \RuntimeException('Flight Quote Ticket Refund saving failed: ' . $flightQuoteTicketRefund->getErrorSummary(true)[0]);
        }
        return $flightQuoteTicketRefund->fqtr_id;
    }
}
