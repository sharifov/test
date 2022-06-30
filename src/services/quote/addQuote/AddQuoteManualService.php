<?php

namespace src\services\quote\addQuote;

use common\models\Airports;
use common\models\Quote;
use common\models\QuoteSegment;
use common\models\QuoteTrip;
use DateTime;
use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;
use src\exception\AdditionalDataException;
use src\helpers\ErrorsToStringHelper;
use src\services\parsingDump\ReservationService;

/**
 * Class AddQuoteManualService
 */
class AddQuoteManualService
{
    public static function createQuoteTripsManually(Quote $quote): array
    {
        $result = [
            'trips' => 0,
            'segments' => 0,
        ];
        if ($data = $quote->getTripsSegmentsData()) {
            foreach ($data as $tripEntry) {
                $trip = new QuoteTrip();
                $trip->qt_duration = $tripEntry['qt_duration'];
                $trip->qt_key = $tripEntry['qt_key'];

                if (!$trip->validate()) {
                    throw new AdditionalDataException(
                        [
                            'quoteUid' => $quote->uid,
                            'reservationDump' => $quote->reservation_dump,
                        ],
                        'QuoteTrip not saved. ' . ErrorsToStringHelper::extractFromModel($trip),
                        -1
                    );
                }
                $quote->link('quoteTrips', $trip);
                $result['trips']++;

                if (isset($tripEntry['segments']) && is_array($tripEntry['segments'])) {
                    $i = 1;
                    foreach ($tripEntry['segments'] as $segmentEntry) {
                        $segment = new QuoteSegment();
                        $segment->scenario = QuoteSegment::SCENARIO_MANUALLY;
                        $segment->attributes = $segmentEntry;
                        if (!$segment->validate()) {
                            throw new AdditionalDataException(
                                [
                                    'quoteUid' => $quote->uid,
                                    'reservationDump' => $quote->reservation_dump,
                                    'segmentEntry' => $segmentEntry,
                                ],
                                'QuoteSegment not saved. ' . ErrorsToStringHelper::extractFromModel($segment) . ' Line: #' . $i,
                                -1
                            );
                        }
                        $trip->link('quoteSegments', $segment);
                        $result['segments']++;
                        $i++;
                    }
                }
            }
        }
        return $result;
    }

    public static function getPastSegmentsByProductQuote($gds, $productQuote): array
    {
        $reservationService = new ReservationService($gds);
        $productFlightQuote = $productQuote->flightQuote;
        $countPastTrips = 0;
        $pastSegmentsItinerary = $pastParsedSegments = [];
        $countTrips = count($productFlightQuote->flightQuoteTrips);
        if ($countTrips) {
            foreach ($productFlightQuote->flightQuoteTrips as $trip) {
                $issetPastSegments = false;
                if (count($trip->flightQuoteSegments)) {
                    foreach ($trip->flightQuoteSegments as $key => $segment) {
                        $departureTimeZone = null;
                        if ($departureAirport = Airports::findByIata($segment->fqs_departure_airport_iata)) {
                            $departureTimeZone = new \DateTimeZone($departureAirport->timezone);
                        }
                        $departureDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $segment->fqs_departure_dt, $departureTimeZone);
                        $now = new DateTime('now', $departureTimeZone);

                        if ($now->getTimestamp() >= $departureDateTime->getTimestamp()) {
                            $issetPastSegments = true;
                            $segment = $reservationService->parseSegment($pastParsedSegments, $key + 1, $segment);
                            $pastParsedSegments[] = $segment;
                            $pastSegmentsItinerary[] = (new ItineraryDumpDTO([]))
                                ->feelByParsedReservationDump($segment);
                        }
                    }
                }
                if ($issetPastSegments) {
                    $countPastTrips++;
                }
            }
        }

        return [$pastSegmentsItinerary, $pastParsedSegments, $countPastTrips];
    }

    public static function updateSegmentTripFormsData($form, $totalTrips, $pastSegmentsItinerary): array
    {
        $newSegmentTripFormData = $form->getSegmentTripFormsData();
        $updatedSegmentsTrip = $pastSegmentTripFormData = [];

        foreach ($pastSegmentsItinerary as $item) {
            $pastSegmentTripFormData['SegmentTripForm_' . $item->departureAirportCode . $item->destinationAirportCode] = [
                'segment_iata' => $item->departureAirportCode . $item->destinationAirportCode,
                'segment_trip_key' => '1'
            ];
        }

        foreach ($newSegmentTripFormData as $keyTripForm => $value) {
            if (!is_array($value) || !array_key_exists('segment_iata', $value) || !array_key_exists('segment_trip_key', $value)) {
                continue;
            }
            $updatedSegmentsTrip[$keyTripForm] = [
                'segment_iata' => $value['segment_iata'],
                'segment_trip_key' => $totalTrips,
            ];
        }

        return array_merge($pastSegmentTripFormData, $updatedSegmentsTrip);
    }

    public static function updateKeyTripList($form, $totalPastTrips): string
    {
        if ($totalPastTrips > 0) {
            while ($totalPastTrips > 0) {
                $receivedTrips = explode(',', $form->keyTripList);
                $addTrip = count($receivedTrips) + 1;
                $form->keyTripList = $form->keyTripList . ',' . $addTrip;
                $totalPastTrips--;
            }
        }

        return $form->keyTripList;
    }

    public static function updateFormAndMergeSegments($form, $totalPastTrips, $pastSegmentsItinerary, $itinerary, $pastSegments, $segments): array
    {
        $form->keyTripList = self::updateKeyTripList($form, $totalPastTrips);
        $form->itinerary = array_merge($pastSegmentsItinerary, $itinerary);
        $mergedSegments = array_merge($pastSegments, $segments);
        $totalTrips = count(explode(',', $form->keyTripList));

        return [$form, $mergedSegments, $totalTrips];
    }
}
