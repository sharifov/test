<?php

namespace modules\flight\src\services\flightQuote;

use modules\flight\models\FlightQuote;
use modules\order\src\events\OrderFileGeneratedEvent;
use src\services\pdf\processingPdf\PdfBaseService;

/**
 * Class FlightQuotePdfService
 */
class FlightQuotePdfService extends PdfBaseService
{
    public $templateKey = 'pdf_ticket_issued';
    public $eventType = OrderFileGeneratedEvent::TYPE_FLIGHT_CONFIRMATION;

    public function fillData()
    {
        $this->communicationData['flight_quote'] = $this->object->serialize();
        $this->communicationData['project_key'] = $this->projectKey;
        $this->communicationData['order'] = $this->object->getOrder() ? $this->object->getOrder()->serialize() : null;
        return $this;
    }

    public static function guard(FlightQuote $flightQuote): void
    {
        if (!$flightQuote->fq_ticket_json) {
            throw new \RuntimeException('FlightQuote: ticket_json is empty');
        }
    }
}
