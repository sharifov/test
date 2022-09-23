<?php

namespace modules\quoteAward\src\services;

use modules\flight\src\helpers\FlightQuoteHelper;
use modules\quoteAward\src\forms\AwardQuoteForm;
use modules\quoteAward\src\forms\ImportGdsDumpForm;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\services\parsingDump\ReservationService;
use src\services\quote\addQuote\guard\GdsByQuoteGuard;

class QuoteFlightService
{
    public function save(AwardQuoteForm $awardQuoteForm)
    {
        // TODO
    }

    public function importGdsDump(ImportGdsDumpForm $form): array
    {
        if (!$form->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($form, ' '));
        }

        $gds = GdsByQuoteGuard::guard($form->gds);
        $reservationService = new ReservationService($gds);
        $reservationService->parseReservation($form->reservationDump, true, $itinerary);

        if (!$reservationService->parseStatus || empty($reservationService->parseResult)) {
            throw new \RuntimeException('Parsing dump is failed');
        }

        $segments = $reservationService->parseResult;
        $trips = [];
        $tripIndex = 1;
        foreach ($segments as $key => $segment) {
            if ($key === 0) {
                $trips[$tripIndex]['duration'] = $segment['flightDuration'];
                $segment['tripIndex'] = $tripIndex;
                $trips[$tripIndex]['segments'][] = $segment;
            } else {
                $prevSegment = $segments[$key - 1] ?? $segments[$key];
                $isNextTrip = false;

                try {
                    $isNextTrip = FlightQuoteHelper::isNextTrip($prevSegment, $segment);
                } catch (\Throwable $throwable) {
                    \Yii::warning(
                        AppHelper::throwableLog($throwable),
                        'QuoteAwardController:actionImportGdsDump:isNextTrip'
                    );
                }

                if ($isNextTrip) {
                    ++$tripIndex;
                    $trips[$tripIndex]['duration'] = $segment['flightDuration'] + $segment['layoverDuration'];
                } else {
                    $trips[$tripIndex]['duration'] += $segment['flightDuration'] + $segment['layoverDuration'];
                }
                $segment['tripIndex'] = $tripIndex;
                $trips[$tripIndex]['segments'][] = $segment;
            }
        }

        if (empty($trips)) {
            throw new \RuntimeException('Trips processing failed');
        }

        return $trips;
    }
}
