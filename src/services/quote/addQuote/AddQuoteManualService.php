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
use yii\helpers\ArrayHelper;

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
        $pastParsedSegments = [];
        $totalFlightTrips = count($productFlightQuote->flightQuoteTrips);
        if ($totalFlightTrips) {
            foreach ($productFlightQuote->flightQuoteTrips as $tripKey => $trip) {
                if (count($trip->flightQuoteSegments)) {
                    foreach ($trip->flightQuoteSegments as $key => $segment) {
                        $departureTimeZone = null;
                        if ($departureAirport = Airports::findByIata($segment->fqs_departure_airport_iata)) {
                            $departureTimeZone = new \DateTimeZone($departureAirport->timezone);
                        }
                        $departureDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $segment->fqs_departure_dt, $departureTimeZone);
                        $now = new DateTime('now', $departureTimeZone);

                        if ($now->getTimestamp() >= $departureDateTime->getTimestamp()) {
                            $segment = $reservationService->parseSegment($pastParsedSegments, $key + 1, $segment);
                            $segment['segment_trip_key'] = $tripKey + 1;
                            $pastParsedSegments[] = $segment;
                        }
                    }
                }
            }
        }

        return [$pastParsedSegments, $totalFlightTrips];
    }

    public static function updateSegmentTripFormsData($form, $pastSegmentsItinerary): array
    {
        $newSegmentTripFormData = $form->getSegmentTripFormsData();
        $updatedSegmentsTrip = $pastSegmentTripFormData = [];

        $tripKey = 0;
        foreach ($pastSegmentsItinerary as $item) {
            $tripKey = $item->tripKey ?? 1;
            $pastSegmentTripFormData['SegmentTripForm_' . $item->departureAirportCode . $item->destinationAirportCode] = [
                'segment_iata' => $item->departureAirportCode . $item->destinationAirportCode,
                'segment_trip_key' => $tripKey
            ];
        }

        foreach ($newSegmentTripFormData as $keyTripForm => $value) {
            if (!is_array($value) || !array_key_exists('segment_iata', $value) || !array_key_exists('segment_trip_key', $value)) {
                continue;
            }
            $updatedSegmentsTrip[$keyTripForm] = [
                'segment_iata' => $value['segment_iata'],
                'segment_trip_key' => $tripKey + (int)$value['segment_trip_key'],
            ];
        }

        return array_merge($pastSegmentTripFormData, $updatedSegmentsTrip);
    }

    public static function updateFormAndMergeSegments($form, $itinerary, $pastSegments, $segments, $totalFlightTrips): array
    {
        $pastSegmentsItinerary = [];
        $totalFormTrips = count(explode(',', $form->keyTripList));
        $totalPastTrips = count($pastSegments);

        if ($totalPastTrips === 0 || $totalFlightTrips === $totalPastTrips) {
            $mergedSegments = $segments;
        } else {
            $totalFormTripsUpdated = (int)$totalFormTrips + (int)$totalPastTrips;
            $form->keyTripList = implode(',', range(1, $totalFormTripsUpdated));

            foreach ($pastSegments as $pastSegment) {
                $pastSegmentsItinerary[] = (new ItineraryDumpDTO([]))->feelByParsedReservationDump($pastSegment);
            }
            $form->itinerary = array_merge($pastSegmentsItinerary, $itinerary);
            $mergedSegments = array_merge($pastSegments, $segments);
            $totalFormTrips = count(explode(',', $form->keyTripList));
        }

        $updatedSegmentTripFormData = AddQuoteManualService::updateSegmentTripFormsData($form, $pastSegmentsItinerary);
        $form->setSegmentTripFormsData($updatedSegmentTripFormData);

        return [$form, $mergedSegments, $totalFormTrips];
    }
}
