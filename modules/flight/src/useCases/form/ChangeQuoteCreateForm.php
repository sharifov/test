<?php

namespace modules\flight\src\useCases\form;

use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;
use modules\flight\src\useCases\flightQuote\createManually\FlightQuotePaxPriceForm;
use DateTime;
use src\helpers\setting\SettingHelper;

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
 * @property string|null $expirationDate
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
    public ?string $expirationDate = null;

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

    /**
     * @param DateTime $date
     * @return void
     */
    public function setExpirationDate(DateTime $date): void
    {
        $maxDate = $date->modify(sprintf('-%d hours', SettingHelper::getMinHoursDifferenceOffers()));
        $date = (new DateTime())->modify(sprintf('+%d days', SettingHelper::getExpirationDaysOfNewOffers()));

        $this->expirationDate = $date > $maxDate ? $maxDate->format('Y-m-d') : $date->format('Y-m-d');
    }
}
