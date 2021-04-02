<?php

namespace sales\services\quote\addQuote;

use common\models\Quote;
use common\models\QuoteSegment;
use common\models\QuoteTrip;
use sales\exception\AdditionalDataException;
use sales\helpers\ErrorsToStringHelper;

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
}
