<?php

namespace modules\flight\src\services\flightQuoteRefund;

use modules\flight\models\FlightQuoteTicket;
use modules\product\src\interfaces\ProductQuoteObjectRefundStructure;
use modules\product\src\interfaces\ProductQuoteRefundService;
use sales\repositories\NotFoundException;

class FlightQuoteRefundService implements ProductQuoteRefundService
{
    public function getRefundStructureObject(int $id): ProductQuoteObjectRefundStructure
    {
        $flightQuoteTicket = FlightQuoteTicket::findOne(['ftr_id' => $id]);
        if (!$flightQuoteTicket) {
            throw new NotFoundException('Not found flight ticket');
        }

        return new FlightQuoteRefundStructure(
            $flightQuoteTicket->fqt_ticket_number,
            $flightQuoteTicket->fqtFqb->fqb_booking_id,
            $flightQuoteTicket->fqtFqb->fqb_pnr,
            $flightQuoteTicket->fqtFqb->fqb_gds,
            $flightQuoteTicket->fqtFqb->fqb_gds_pcc,
        );
    }
}
