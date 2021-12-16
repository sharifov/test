<?php

namespace modules\flight\src\useCases\form;

use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;
use modules\flight\src\useCases\flightQuote\createManually\FlightQuotePaxPriceForm;

/**
 * Class ChangeQuoteCreateForm
 *
 * @property $recordLocator
 * @property $gds
 * @property $pcc
 * @property $tripType
 * @property $cabin
 * @property $validatingCarrier
 * @property $fareType
 * @property $reservationDump
 * @property $quoteCreator
 * @property $baggage_data
 * @property $segment_trip_data
 * @property $keyTripList
 * @property $flightId
 *
 * @property ItineraryDumpDTO[] $itinerary
 * @property array $baggageFormsData
 * @property array $segmentTripFormsData
 * @property FlightQuotePaxPriceForm[] $flightQuotePaxPriceForms
 */
class ChangeQuoteCreateForm extends \yii\base\Model
{
    public $recordLocator;
    public $gds;
    public $pcc;
    public $tripType;
    public $cabin;
    public $validatingCarrier;
    public $fareType;
    public $quoteCreator;
    public $reservationDump;
    public $baggage_data;
    public $segment_trip_data;
    public $keyTripList;
    public $flightId;

    private array $itinerary = [];
    private array $baggageFormsData = [];
    private array $segmentTripFormsData = [];
    private array $flightQuotePaxPriceForms = [];

    public function getBaggageFormsData(): array
    {
        return $this->baggageFormsData;
    }

    public function getItinerary(): array
    {
        return $this->itinerary;
    }

    public function getSegmentTripFormsData(): array
    {
        return $this->segmentTripFormsData;
    }

    public function getFlightQuotePaxPriceForms(): array
    {
        return $this->flightQuotePaxPriceForms;
    }
}
