<?php

namespace modules\flight\src\services\flightQuote;

use modules\flight\models\FlightQuote;
use modules\flight\src\services\flightQuoteBooking\FlightQuoteBookingPdfService;
use yii\helpers\Inflector;

/**
 * Class FlightQuoteTicketIssuedService
 */
class FlightQuoteTicketIssuedService
{
    public static function generateTicketIssued(FlightQuote $flightQuote, bool $isResultData = false): array
    {
        if (!$flightQuote->flightQuoteFlights) {
            throw new \DomainException('Flights not related in FlightQuote Id(' . $flightQuote->fq_id . ')');
        }

        $processedBooking = [];
        foreach ($flightQuote->flightQuoteFlights as $flightQuoteFlight) {
            foreach ($flightQuoteFlight->flightQuoteBookings as $flightQuoteBooking) {
                $flightQuoteBookingPdfService = new FlightQuoteBookingPdfService($flightQuoteBooking);

                $identity = $flightQuoteFlight->fqfFq->fq_uid . '-' . $flightQuoteBooking->fqb_booking_id;
                $fileName = $flightQuoteBookingPdfService->templateKey . '-' . $identity;
                $fileTitle = Inflector::camelize($flightQuoteBookingPdfService->templateKey) . '-' . $identity;

                $flightQuoteBookingPdfService->setProductQuoteId($flightQuoteFlight->fqfFq->fq_product_quote_id);
                $flightQuoteBookingPdfService->setFileName($fileName);
                $flightQuoteBookingPdfService->setFileTitle($fileTitle);
                $flightQuoteBookingPdfService->processingFile();

                $processedBooking[$flightQuoteBooking->fqb_id] =
                    $isResultData ? $flightQuoteBookingPdfService->getCommunicationData() : $flightQuoteBooking->fqb_id;
            }
        }
        return $processedBooking;
    }
}
