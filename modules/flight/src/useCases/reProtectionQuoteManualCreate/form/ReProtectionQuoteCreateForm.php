<?php

namespace modules\flight\src\useCases\reProtectionQuoteManualCreate\form;

use common\components\validators\NormalizeDateValidator;
use common\models\Airline;
use common\models\Employee;
use frontend\helpers\JsonHelper;
use modules\flight\models\Flight;
use modules\flight\models\FlightQuote;
use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;
use modules\flight\src\helpers\FlightQuoteHelper;
use modules\flight\src\useCases\form\ChangeQuoteCreateForm;
use src\services\parsingDump\ReservationService;
use yii\base\Model;
use yii\helpers\Html;

/**
 * Class ReProtectionQuoteCreateForm
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
 */
class ReProtectionQuoteCreateForm extends ChangeQuoteCreateForm
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

    public function __construct(?int $creatorId = null, ?int $flightId = null, $config = [])
    {
        $this->quoteCreator = $creatorId;
        $this->flightId = $flightId;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['gds', 'pcc', 'tripType', 'cabin', 'validatingCarrier', 'fareType', 'reservationDump'], 'string'],
            [['gds', 'validatingCarrier', 'cabin', 'tripType', 'fareType', 'reservationDump', 'quoteCreator', 'keyTripList', 'expirationDate'], 'required'],
            ['quoteCreator', 'integer'],

            [['reservationDump'], 'string'],
            ['recordLocator', 'string', 'max' => 8],
            ['pcc', 'string', 'max' => 10],
            [['quoteCreator'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['quoteCreator' => 'id']],
            ['gds', 'in',  'range' => array_keys(FlightQuote::getGdsList())],
            ['tripType', 'in',  'range' => array_keys(Flight::getTripTypeList())],
            ['validatingCarrier', 'in',  'range' => array_keys(Airline::getAirlinesMapping(true))],
            ['fareType', 'in',  'range' => array_keys(FlightQuote::getFareTypeList())],
            ['cabin', 'in',  'range' => array_keys(Flight::getCabinClassList())],
            [['reservationDump'], 'checkReservationDump'],
            [['baggage_data'], 'string'],
            [['baggage_data'], 'baggageDataHandle'],
            [['segment_trip_data'], 'safe'],
            [['segment_trip_data'], 'segmentTripPrepare'],
            [['keyTripList'], 'string'],

            [['flightId'], 'integer'],
            [['flightId'], 'exist', 'skipOnError' => true, 'targetClass' => Flight::class, 'targetAttribute' => ['flightId' => 'fl_id']],
            ['expirationDate', NormalizeDateValidator::class],
            ['expirationDate', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function segmentTripPrepare(): void
    {
        if (!empty($this->segment_trip_data)) {
            parse_str($this->segment_trip_data, $this->segmentTripFormsData);
        }
    }

    public function baggageDataHandle(): void
    {
        if (!empty($this->baggage_data)) {
            parse_str($this->baggage_data, $this->baggageFormsData);
        }
    }

    public function checkReservationDump(): void
    {
        try {
            $reservationService = new ReservationService($this->gds);
            $reservationService->parseReservation($this->reservationDump, false, $this->itinerary);
        } catch (\Throwable $throwable) {
            $this->addError('reservationDump', $throwable->getMessage());
        }
        if (!($reservationService->parseStatus ?? null) || empty($this->itinerary)) {
            $this->addError('reservationDump', 'Incorrect reservation dump');
        }
    }

    public function formName(): string
    {
        return '';
    }

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
}
