<?php

namespace modules\rentCar\src\services;

use modules\order\src\events\OrderFileGeneratedEvent;
use sales\services\pdf\processingPdf\PdfBaseService;

/**
 * Class RentCarQuotePdfService
 */
class RentCarQuotePdfService extends PdfBaseService
{
    public $templateKey = 'pdf_car_rental';
    public $eventType = OrderFileGeneratedEvent::TYPE_RENT_CAR_CONFIRMATION;

    public function fillData()
    {
        $this->communicationData['rent_car_quote'] = $this->object->serialize();
        $this->communicationData['project_key'] = $this->projectKey;
        $this->communicationData['order'] = $this->object->getOrder() ? $this->object->getOrder()->serialize() : null;
        return $this;
    }
}
