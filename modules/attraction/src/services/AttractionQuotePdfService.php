<?php

namespace modules\attraction\src\services;

use modules\attraction\models\AttractionQuote;
use modules\order\src\events\OrderFileGeneratedEvent;
use sales\services\pdf\processingPdf\PdfBaseService;

/**
 * Class AttractionQuotePdfService
 */
class AttractionQuotePdfService extends PdfBaseService
{
    public $templateKey = 'pdf_activity_eticket';
    public $eventType = OrderFileGeneratedEvent::TYPE_ATTRACTION_CONFIRMATION;

    public function fillData()
    {
        /** @var AttractionQuote $attractionQuote */
        $attractionQuote = $this->object;
        $this->communicationData = (new RequestPdfDataGenerator())->generate($attractionQuote);
        return $this;
    }
}
