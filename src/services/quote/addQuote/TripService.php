<?php

namespace src\services\quote\addQuote;

use common\models\Log;
use common\models\Quote;
use common\models\QuoteSegment;
use common\models\QuoteTrip;
use src\exception\AdditionalDataException;
use src\helpers\ErrorsToStringHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * Class TripService
 */
class TripService
{
    private Quote $quote;
    private array $errors = [];

    /**
     * @param Quote $quote
     */
    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    /**
     * @param array $parsedData
     * @return array
     */
    public function createTrips(array $parsedData): array
    {
        $result = [];

        if (empty($parsedData)) {
            throw new AdditionalDataException(
                [
                    'quoteUid' => $this->quote->uid,
                    'reservationDump' => $this->quote->reservation_dump,
                ],
                'Parsed "reservation_dump" cannot be empty.',
                -1
            );
        }

        foreach ($parsedData as $tripEntry) {
            $this->guardTripEntry($tripEntry);

            $trip = new QuoteTrip();
            $trip->qt_duration = $tripEntry['qt_duration'];
            $trip->qt_key = $tripEntry['qt_key'];

            if (!$trip->validate()) {
                throw new AdditionalDataException(
                    [
                        'quoteUid' => $this->quote->uid,
                        'reservationDump' => $this->quote->reservation_dump,
                        'tripEntry' => $tripEntry,
                    ],
                    'QuoteTrip not created. ' . ErrorsToStringHelper::extractFromModel($trip),
                    -1
                );
            }
            $this->quote->link('quoteTrips', $trip);

            $result[$trip->qt_id]['trip'] = $trip;

            foreach ($tripEntry['segments'] as $segmentEntry) {
                $segment = new QuoteSegment();
                $segment->attributes = $segmentEntry;
                if (!$segment->validate()) {
                    throw new AdditionalDataException(
                        [
                            'quoteUid' => $this->quote->uid,
                            'reservationDump' => $this->quote->reservation_dump,
                            'segmentDump' => VarDumper::dumpAsString($segmentEntry),
                        ],
                        'QuoteSegment not created. ' . ErrorsToStringHelper::extractFromModel($segment),
                        -1
                    );
                }
                $trip->link('quoteSegments', $segment);
                $result[$trip->qt_id]['segments'] = $segment;
            }
        }
        return $result;
    }

    private function guardTripEntry(array $tripEntry): bool
    {
        if (ArrayHelper::keyExists('qt_duration', $tripEntry) && ArrayHelper::keyExists('qt_key', $tripEntry)) {
            return true;
        }

        throw new AdditionalDataException(
            [
                'quoteUid' => $this->quote->uid,
                'reservationDump' => $this->quote->reservation_dump,
                'TripEntry' => VarDumper::dumpAsString($tripEntry),
            ],
            'Keys "qt_duration", "qt_key" and "segments" must be in an "TripEntry"',
            -1
        );
    }

    public function getQuote(): Quote
    {
        return $this->quote;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
