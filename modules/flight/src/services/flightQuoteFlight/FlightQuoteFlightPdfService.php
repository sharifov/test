<?php

namespace modules\flight\src\services\flightQuoteFlight;

use modules\order\src\events\OrderFileGeneratedEvent;
use sales\services\pdf\processingPdf\PdfBaseService;

/**
 * Class FlightQuoteFlightPdfService
 */
class FlightQuoteFlightPdfService extends PdfBaseService
{
    public $templateKey = 'pdf_flight_ticket_issued';
    public $eventType = OrderFileGeneratedEvent::TYPE_FLIGHT_CONFIRMATION;

    public function fillData()
    {
        /* TODO:: to remove */
        $this->communicationData['flight_quote_flight'] = $this->object->serialize();
        $this->communicationData['project_key'] = $this->projectKey;
        return $this;
    }
}
