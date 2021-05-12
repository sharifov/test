<?php

namespace modules\flight\src\services\flightQuoteBooking;

use modules\flight\models\FlightQuoteBooking;
use modules\order\src\events\OrderFileGeneratedEvent;
use sales\services\pdf\processingPdf\PdfBaseService;

/**
 * Class FlightQuoteBookingPdfService
 */
class FlightQuoteBookingPdfService extends PdfBaseService
{
    public $templateKey = 'pdf_flight_ticket_issued';
    public $eventType = OrderFileGeneratedEvent::TYPE_FLIGHT_CONFIRMATION;

    public function fillData()
    {
        /** @var flightQuoteBooking $flightQuoteBooking */
        $flightQuoteBooking = $this->object;
        $this->communicationData['flight_quote_booking'] = $flightQuoteBooking->serialize();
        $this->communicationData['flight_quote_flight'] = $flightQuoteBooking->fqbFqf->serialize(false);
        $this->communicationData['project_key'] = $this->projectKey;
        return $this;
    }
}
