<?php

namespace sales\services\quote\addQuote;

use common\models\Log;
use common\models\Quote;
use common\models\QuoteSegment;
use common\models\QuoteTrip;
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
            $message = 'Parsed "reservation_dump" cannot be empty.';
                $message .= ' ' . Log::ADDITIONAL_DATA_DELIMITER .
                    '. Dump: ' . $this->quote->reservation_dump .
                    '. QuoteUid: ' . $this->quote->uid;
            throw new \DomainException($message, -1);
        }

        foreach ($parsedData as $tripEntry) {
            $this->guardTripEntry($tripEntry);

            $trip = new QuoteTrip();
            $trip->qt_duration = $tripEntry['qt_duration'];
            $trip->qt_key = $tripEntry['qt_key'];

            if (!$trip->validate()) {
                $message = 'QuoteTrip not created. Error: ' . $trip->getErrorSummary(false)[0];
                $message .= ' ' . Log::ADDITIONAL_DATA_DELIMITER .
                    '. Dump: ' . $this->quote->reservation_dump .
                    '. QuoteUid: ' . $this->quote->uid;

                throw new \DomainException($message, -1);
            }
            $this->quote->link('quoteTrips', $trip);

            $result[$trip->qt_id]['trip'] = $trip;

            foreach ($tripEntry['segments'] as $segmentEntry) {
                $segment = new QuoteSegment();
                $segment->attributes = $segmentEntry;
                if (!$segment->validate()) {
                    $message = 'QuoteSegment not created.' .
                        '. Error: ' . $segment->getErrorSummary(false)[0];
                    $message .= ' ' . Log::ADDITIONAL_DATA_DELIMITER .
                        '. SegmentDump: ' . VarDumper::dumpAsString($segmentEntry) .
                        '. QuoteDump: ' . $this->quote->reservation_dump;

                    throw new \DomainException($message, -1);
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

        $message = 'Keys "qt_duration", "qt_key" and "segments" must be in an "TripEntry".';
        $message .= ' ' . Log::ADDITIONAL_DATA_DELIMITER .
            '. TripEntry: . ' . VarDumper::dumpAsString($tripEntry) .
            '. Dump: ' . $this->quote->reservation_dump .
            '. QuoteUid' . $this->quote->uid;

        throw new \DomainException($message, -1);
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
