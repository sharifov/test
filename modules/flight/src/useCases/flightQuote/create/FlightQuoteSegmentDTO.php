<?php

namespace modules\flight\src\useCases\flightQuote\create;

use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteTrip;

/**
 * Class FlightQuoteSegmentDTO
 * @package modules\flight\src\useCases\flightQuote\create
 */
class FlightQuoteSegmentDTO implements FlightQuoteSegmentDTOInterface
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
     * FlightQuoteSegmentDTO constructor.
     * @param FlightQuote $flightQuote
     * @param FlightQuoteTrip $flightQuoteTrip
     * @param array $segment
     * @param mixed $ticketId
     */
    public function __construct(FlightQuote $flightQuote, FlightQuoteTrip $flightQuoteTrip, array $segment, $ticketId = null)
    {
        $this->flightQuoteId = $flightQuote->fq_id;
        $this->flightQuoteTripId = $flightQuoteTrip->fqt_id;
        $this->departureDt = $segment['departureTime'] ?? '';
        $this->arrivalDt = $segment['arrivalTime'] ?? '';
        $this->stop = $segment['stop'] ?? null;
        $this->flightNumber = $segment['flightNumber'] ?? '';
        $this->bookingClass = $segment['bookingClass'] ?? '';
        $this->duration = $segment['duration'] ?? '';
        $this->departureAirportIata = $segment['departureAirportCode'] ?? '';
        $this->departureAirportTerminal = $segment['departureAirportTerminal'] ?? '';
        $this->arrivalAirportIata = $segment['arrivalAirportCode'] ?? '';
        $this->arrivalAirportTerminal = $segment['arrivalAirportTerminal'] ?? '';
        $this->operatingAirline = $segment['operatingAirline'] ?? '';
        $this->marketingAirline = $segment['marketingAirline'] ?? '';
        $this->airEquipType = $segment['airEquipType'] ?? '';
        $this->marriageGroup = $segment['marriageGroup'] ?? '';
        $this->cabinClass = $segment['cabin'] ?? '';
        $this->cabinClassBasic = $segment['cabinIsBasic'] ?? false;
        $this->meal = $segment['meal'] ?? '';
        $this->fareCode = $segment['fareCode'] ?? '';
        $this->ticketId = $ticketId;
        $this->recheckBaggage = !empty($segment['recheckBaggage']) ? 1 : 0;
        $this->mileage = $segment['mileage'] ?? '';
        $this->key = '#' . $this->flightNumber .
            ($this->stop > 0 ? '(' . $this->stop . ')' : '') .
            $this->departureAirportIata . '-' . $this->arrivalAirportIata . ' ' . $this->departureDt;
    }
}
