<?php

namespace modules\flight\src\dto\itineraryDump;

/**
 * Class ItineraryDumpDTO
 * @package modules\flight\src\dto\itineraryDump
 */
class ItineraryDumpDTO
{
    public $airlineCode;
    public $departureAirportCode;
    public $destinationAirportCode;
    public $departureTime;
    public $arrivalTime;
    public $flightNumber;
    public $mainAirlineCode;
    public $duration;
    public $bookingClass;
    public $cabin;
    public $operationAirlineCode;
    public $operationAirline;
    public $marketingAirlineCode;
    public $marketingAirline;
    public $aircraftCode;
    public $aircraftModel;
    public $airlineRecordLocator;
    public $baggageAllowanceNumber;
    public $statusCode;
    public $arrivalAirportCode;
    public ?int $tripKey = null;

    /**
     * ItineraryDumpDTO constructor.
     * @param array $segment
     */
    public function __construct(array $segment)
    {
        $this->departureTime = $segment['departureTime'] ?? null;
        $this->arrivalTime = $segment['arrivalTime'] ?? null;
        $this->airlineCode = $segment['marketingAirline'] ?? null;
        $this->flightNumber = $segment['flightNumber'] ?? null;
        $this->bookingClass = $segment['bookingClass'] ?? null;
        $this->departureAirportCode = $segment['departureAirportCode'] ?? null;
        $this->destinationAirportCode = $segment['arrivalAirportCode'] ?? null;
        $this->tripKey = $segment['segment_trip_key'] ?? null;
        if (isset($segment['operatingAirline'], $segment['marketingAirline'])) {
            $this->operationAirlineCode = $segment['operatingAirline'] !== $segment['marketingAirline'] ? ($segment['operatingAirline']  ?? null) : null;
        }
    }

    public function feelByParsedReservationDump(array $segment): self
    {
        $this->airlineCode = $segment['carrier'] ?? null;
        $this->bookingClass = $segment['bookingClass'] ?? null;
        $this->cabin = $segment['cabin'] ?? null;
        $this->flightNumber = $segment['flightNumber'] ?? null;
        $this->departureAirportCode = $segment['departureAirport'] ?? null;
        $this->destinationAirportCode = $segment['arrivalAirport'] ?? null;
        $this->departureTime = $segment['departureDateTime']->format('Y-m-d H:i:s');
        $this->arrivalTime = $segment['arrivalDateTime']->format('Y-m-d H:i:s');
        $this->arrivalAirportCode = $segment['arrivalAirport'] ?? null;
        $this->marketingAirlineCode = $segment['carrier'] ?? null;
        $this->operationAirlineCode = $segment['operatingAirline'] ?? $this->marketingAirlineCode;
        $this->operationAirline = $segment['operatingAirlineName'] ?? null;
        $this->marketingAirline = $segment['carrier'] ?? null;
        $this->duration = $segment['flightDuration'] ?? null;
        $this->tripKey = $segment['segment_trip_key'] ?? null;
        return $this;
    }
}
