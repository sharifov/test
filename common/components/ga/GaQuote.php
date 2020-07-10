<?php

namespace common\components\ga;

use common\models\Lead;
use common\models\Quote;
use sales\helpers\app\AppHelper;
use Yii;
use yii\httpclient\Response;

/**
 * Class GaQuote
 *
 * @property string $cid
 * @property string $tid
 * @property array $postData
 */
class GaQuote
{
    private $cid; // Client ID
    private $tid; // Tracking ID
    private array $postData = [];

    private Quote $quote;
    private Lead $lead;

    /**
     * @param Quote $quote
     */
    public function __construct(Quote $quote)
    {
        GaHelper::checkSettings(GaHelper::TYPE_QUOTE);

        $this->quote = $quote;
        $this->lead = $quote->lead;

        $this->setCid()
            ->setTid()
            ->setPostData();
    }

    private function setTid(): GaQuote
    {
        if ($this->tid = GaHelper::getTrackingIdByLead($this->lead)) {
            return $this;
        }
        throw new \DomainException('GA Tracking ID not found.', -4);
    }

    private function setCid(): GaQuote
    {
        if ($this->cid = GaHelper::getClientIdByLead($this->lead)) {
            return $this;
        }
        throw new \DomainException('GA Client Id not found.', -2);
    }

    /**
     * @return GaQuote
     */
    private function setPostData(): GaQuote
    {
        try {
            $this->postData = [
                'tid' => $this->tid,
                'cid' => $this->cid,
                't' => 'event',
                'ec' => 'leads',
                'ea' => 'phone-to-book',
                'el' => 'mail-sent',
                'cd1' => $this->cid,
                'cd2' => '',
            ];

            $this->postData['cd3'] = self::getOriginByQuote($this->quote);
            $this->postData['cd4'] = self::getDestinationByQuote($this->quote);
            $this->postData['cd5'] = self::getDateDeparture($this->quote);
            $this->postData['cd6'] = self::getDateDepartureRoundTrip($this->quote);
            $this->postData['cd8'] = implode('-', self::getAirportCodes($this->quote));
            $this->postData['cd9'] = Lead::getFlightType($this->quote->trip_type);

            $this->postData['cd10'] = implode(',', $this->getOperatingAirlines());
            $this->postData['cd11'] = implode(',', $this->getMarketingAirlines());

            $this->postData = GaHelper::preparePostData($this->postData, $this->lead);

        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable),
            'GaQuote:prepareData:Throwable');
            $this->postData = [];
        }
        return $this;
    }

    public static function getAirportCodes(Quote $quote): array
    {
        $result = [];
        foreach ($quote->quoteTrips as $trpKey => $trip) {
            foreach ($trip->quoteSegments as  $segmentKey => $segment) {
                if ($trpKey === 0) {
                    $result[] = $segment->qs_departure_airport_code;
                }
                if ($trpKey > 0 && $result[count($result) - 1] !== $segment->qs_departure_airport_code) {
                    $result[] = $segment->qs_departure_airport_code;
                }
                $result[] = $segment->qs_arrival_airport_code;
            }
        }
        return $result;
    }

    /**
     * @return Response|null
     */
    public function send(): ?Response
    {
        try {
            $this->checkPostData();
            return \Yii::$app->gaRequestService->sendRequest($this->postData);
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'GaQuote:prepareData:Throwable');
        }
        return null;
    }

    /**
     * @return bool
     */
    public function checkPostData(): bool
    {
        if (empty($this->postData)) {
            throw new \RuntimeException('Post data is required.', -3);
        }
        return true;
    }

    /**
     * @param Quote $quote
     * @return string
     */
    public static function getDateDepartureRoundTrip(Quote $quote): string
    {
        if ($quote->trip_type === Lead::TRIP_TYPE_ROUND_TRIP) {
            return '';
        }
        $result = [];
        foreach ($quote->quoteTrips as $trip) {
            foreach ($trip->quoteSegments as $segment) {
                if (!empty($segment->qs_arrival_time)) {
                    $result[] = $segment->qs_arrival_time;
                }
            }
        }
        $lastArrivalTime = end($result);
        if ($dateDeparture = date('Y-m-d', strtotime($lastArrivalTime))) {
            return $dateDeparture;
        }
        return '';
    }

    private static function getDateDeparture(Quote $quote): string
    {
        foreach ($quote->quoteTrips as $trip) {
            foreach ($trip->quoteSegments as $segment) {
                if ($dateDeparture = date('Y-m-d', strtotime($segment->qs_departure_time))) {
                    return $dateDeparture;
                }
            }
        }
        return '';
    }

    /**
     * @return array
     */
    private function getOperatingAirlines(): array
    {
        $result = [];
        foreach ($this->quote->quoteTrips as $trip) {
            foreach ($trip->quoteSegments as $segment) {
                if (empty($segment->qs_operating_airline)) {
                    continue;
                }
                $result[$segment->qs_operating_airline] = $segment->qs_operating_airline;
            }
        }
        return $result;
    }

    private function getMarketingAirlines(): array
    {
        $result = [];
        foreach ($this->quote->quoteTrips as $trip) {
            foreach ($trip->quoteSegments as $segment) {
                if (empty($segment->qs_marketing_airline)) {
                    continue;
                }
                $result[$segment->qs_marketing_airline] = $segment->qs_marketing_airline;
            }
        }
        return $result;
    }

    public static function getOriginByQuote(Quote $quote): ?string
    {
        if ($quote->trip_type === Lead::TRIP_TYPE_MULTI_DESTINATION) {
            return implode(',', self::getAllOriginsByQuote($quote));
        }
        $allOrigins = self::getAllOriginsByQuote($quote);
        return current($allOrigins);
    }

    public static function getAllOriginsByQuote(Quote $quote): array
    {
        $result = [];
        foreach ($quote->quoteTrips as $trip) {
            foreach ($trip->quoteSegments as $segment) {
                if (empty($segment->qs_departure_airport_code)) {
                    continue;
                }
                $result[] = $segment->qs_departure_airport_code;
            }
        }
        return $result;
    }

    public static function getAllDestinationByQuote(Quote $quote): array
    {
        $result = [];
        foreach ($quote->quoteTrips as $trip) {
            foreach ($trip->quoteSegments as $segment) {
                if (empty($segment->qs_arrival_airport_code)) {
                    continue;
                }
                $result[] = $segment->qs_arrival_airport_code;
            }
        }
        return $result;
    }

    public static function getDestinationByQuote(Quote $quote): string
    {
        if ($quote->trip_type === Lead::TRIP_TYPE_MULTI_DESTINATION) {
            return implode(',', self::getAllDestinationByQuote($quote));
        }
        return count(self::getAllDestinationByQuote($quote)) ? self::getAllDestinationByQuote($quote)[0] : '';
    }
}