<?php

namespace modules\flight\src\services\flightQuoteRefund;

use modules\product\src\interfaces\ProductQuoteObjectRefundStructure;

class FlightQuoteRefundStructure implements ProductQuoteObjectRefundStructure
{
    public string $ticketNumber;

    public ?string $bookingId;

    public ?string $pnr;

    public ?string $gds;

    public ?string $gdsPcc;

    public function __construct(
        string $ticketNumber,
        ?string $bookingId,
        ?string $pnr,
        ?string $gds,
        ?string $gdsPcc
    ) {
        $this->ticketNumber = $ticketNumber;
        $this->bookingId = $bookingId;
        $this->pnr = $pnr;
        $this->gds = $gds;
        $this->gdsPcc = $gdsPcc;
    }
}
