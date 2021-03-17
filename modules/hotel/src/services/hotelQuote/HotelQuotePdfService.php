<?php

namespace modules\hotel\src\services\hotelQuote;

use modules\hotel\models\HotelQuote;
use modules\order\src\events\OrderFileGeneratedEvent;
use sales\services\pdf\processingPdf\PdfBaseService;

/**
 * Class GeneratorPdfService
 */
class HotelQuotePdfService extends PdfBaseService
{
    public $templateKey = 'hotel_confirmation_pdf';
    public $eventType = OrderFileGeneratedEvent::TYPE_HOTEL_CONFIRMATION;

    public function fillData()
    {
        $this->communicationData['hotel_quote'] = $this->object->serialize();
        $this->communicationData['project_key'] = $this->projectKey;
        $this->communicationData['order'] = $this->object->getOrder() ? $this->object->getOrder()->serialize() : null;
        return $this;
    }

    public static function guard(HotelQuote $hotelQuote): void
    {
        if (!$hotelQuote->hq_booking_id) {
            throw new \RuntimeException('HotelQuote: booking_id is empty');
        }
        if (!$hotelQuote->hq_json_booking) {
            throw new \RuntimeException('HotelQuote: json_booking is empty');
        }
        if (!$hotelQuote->hq_origin_search_data) {
            throw new \RuntimeException('HotelQuote: origin_search_data is empty');
        }
        if (!$hotelQuote->hqHotel) {
            throw new \RuntimeException('Hotel Quote: Hotel not found');
        }
        if (!$hotelQuote->hqProductQuote->pqOrder) {
            throw new \RuntimeException('Hotel Quote: Order not found');
        }
    }
}
