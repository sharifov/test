<?php

namespace modules\flight\src\useCases\flightQuote\create;

use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteTrip;
use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;

/**
 * Class FlightQuoteSegmentDTOItinerary
 */
class FlightQuoteSegmentDTOItinerary implements FlightQuoteSegmentDTOInterface
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
     * @param ItineraryDumpDTO $dto
     */
    public function __construct(int $flightQuoteId, int $flightQuoteTripId, ItineraryDumpDTO $dto)
    {
        $this->flightQuoteId = $flightQuoteId;
        $this->flightQuoteTripId = $flightQuoteTripId;
        $this->departureDt = $dto->departureTime;
        $this->arrivalDt = $dto->arrivalTime;
        $this->stop = null;
        $this->flightNumber = $dto->flightNumber;
        $this->bookingClass = $dto->bookingClass;
        $this->duration = $dto->duration;
        $this->departureAirportIata = $dto->departureAirportCode;
        $this->departureAirportTerminal = null;
        $this->arrivalAirportIata = $dto->arrivalAirportCode;
        $this->arrivalAirportTerminal = null;
        $this->operatingAirline = $dto->operationAirlineCode;
        $this->marketingAirline = $dto->marketingAirlineCode;
        $this->airEquipType = null;
        $this->marriageGroup = null;
        $this->cabinClass = $dto->cabin;
        $this->cabinClassBasic = false;
        $this->meal = null;
        $this->fareCode = null;
        $this->ticketId = null;
        $this->recheckBaggage = null;
        $this->mileage = null;
        $this->key = '#' . $this->flightNumber .
            ((int) $this->stop > 0 ? '(' . $this->stop . ')' : '') .
            $this->departureAirportIata . '-' . $this->arrivalAirportIata . ' ' . $this->departureDt;
    }
}
