<?php

namespace modules\flight\src\dto\flightSegment;

use common\components\SearchService;
use webapi\src\forms\flight\flights\trips\SegmentApiForm;

/**
 * Class FlightQuoteSegmentApiBoDto
 */
class FlightQuoteSegmentApiBoDto
{
    public $flightQuoteId;
    public $flightQuoteTripId;
    public $departureDt;
    public $arrivalDt;
    public $stop;
    public $flightNumber;
    public $bookingClass;
    public $duration;
    public $departureAirportIata;
    public $departureAirportTerminal;
    public $arrivalAirportIata;
    public $arrivalAirportTerminal;
    public $operatingAirline;
    public $marketingAirline;
    public $airEquipType;
    public $marriageGroup;
    public $cabinClass;
    public $cabinClassBasic;
    public $meal;
    public $fareCode;
    public $key;
    public $ticketId;
    public $recheckBaggage;
    public $mileage;

    /**
     * @param int $flightQuoteId
     * @param int $flightQuoteTripId
     * @param SegmentApiForm $segmentApiForm
     */
    public function __construct(int $flightQuoteId, int $flightQuoteTripId, SegmentApiForm $segmentApiForm)
    {
        $this->flightQuoteId = $flightQuoteId;
        $this->flightQuoteTripId = $flightQuoteTripId;

        $this->departureDt = $segmentApiForm->departureTime;
        $this->departureAirportIata = $segmentApiForm->departureAirport;
        $this->departureAirportTerminal = null;

        $this->arrivalDt = $segmentApiForm->arrivalTime;
        $this->arrivalAirportIata = $segmentApiForm->arrivalAirport;
        $this->arrivalAirportTerminal = null;

        $this->flightNumber = $segmentApiForm->flightNumber;
        $this->bookingClass = $segmentApiForm->bookingClass;
        $this->duration = $segmentApiForm->flightDuration;
        $this->operatingAirline = $segmentApiForm->operatingAirlineCode;
        $this->marketingAirline = $segmentApiForm->mainAirline;
        $this->marriageGroup = $segmentApiForm->marriageGroup;
        $this->cabinClass = self::mapCabinCalss($segmentApiForm->cabin);
        $this->cabinClassBasic = $segmentApiForm->cabinIsBasic;
        $this->fareCode = $segmentApiForm->fareCode;
        $this->mileage = $segmentApiForm->mileage;

        $this->stop = null;
        $this->meal = null;
        $this->ticketId = null;
        $this->airEquipType = null;
        $this->recheckBaggage = null;

        $this->key = '#' . $this->flightNumber .
            $this->departureAirportIata . '-' . $this->arrivalAirportIata . ' ' . $this->departureDt;
    }

    public static function mapCabinCalss(?string $cabin)
    {
        if (!$cabin) {
            return null;
        }

        $response = array_filter(SearchService::CABIN_LIST, function ($item) use ($cabin) {
            return mb_strtolower($cabin) === mb_strtolower($item);
        });
        if (count($response)) {
            return key($response);
        }

        if (array_key_exists($cabin, SearchService::CABIN_LIST)) {
            return $cabin;
        }
        return null;
    }
}
