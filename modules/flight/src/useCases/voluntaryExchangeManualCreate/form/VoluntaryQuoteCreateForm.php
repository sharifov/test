<?php

namespace modules\flight\src\useCases\voluntaryExchangeManualCreate\form;

use common\components\validators\IsArrayValidator;
use common\components\validators\NormalizeDateValidator;
use common\models\Airline;
use common\models\Employee;
use modules\flight\models\Flight;
use modules\flight\models\FlightQuote;
use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;
use modules\flight\src\helpers\FlightQuoteHelper;
use modules\flight\src\useCases\flightQuote\createManually\FlightQuotePaxPriceForm;
use modules\flight\src\useCases\flightQuote\createManually\helpers\FlightQuotePaxPriceHelper;
use modules\flight\src\useCases\flightQuote\createManually\VoluntaryQuotePaxPriceForm;
use modules\flight\src\useCases\form\ChangeQuoteCreateForm;
use modules\flight\src\useCases\voluntaryExchangeManualCreate\service\VoluntaryExchangeBOService;
use modules\product\src\entities\productTypePaymentMethod\ProductTypePaymentMethodQuery;
use src\helpers\ErrorsToStringHelper;
use src\helpers\product\ProductQuoteHelper;
use src\services\parsingDump\lib\ParsingDump;
use src\services\parsingDump\ReservationService;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class VoluntaryQuoteCreateForm
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
 * @property $serviceFee
 * @property $currencyRate
 * @property $currencyCode
 * @property string|null $expirationDate
 *
 * @property ItineraryDumpDTO[] $itinerary
 * @property array $baggageFormsData
 * @property array $segmentTripFormsData
 *
 * @property array $prices
 * @property string|null $oldPrices
 * @property FlightQuotePaxPriceForm[] $flightQuotePaxPriceForms
 * @property bool $defaultPrices
 */
class VoluntaryQuoteCreateForm extends ChangeQuoteCreateForm
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
    public $serviceFee;
    public $currencyRate;
    public $currencyCode;

    private array $itinerary = [];
    private array $baggageFormsData = [];
    private array $segmentTripFormsData = [];

    public array $prices = [];
    public $oldPrices;

    private array $flightQuotePaxPriceForms = [];
    private bool $defaultPrices;

    public ?string $customerPackage = null;
    public ?string $serviceFeeCurrency = null;

    public $serviceFeeAmount = null;

    /**
     * @param int|null $creatorId
     * @param Flight|null $flight
     * @param bool $defaultPrices
     * @param float|null $systemMarkUp
     * @param array $config
     */
    public function __construct(
        ?int $creatorId = null,
        ?Flight $flight = null,
        bool $defaultPrices = true,
        ?float $systemMarkUp = null,
        $config = []
    ) {
        $this->quoteCreator = $creatorId;
        $this->flightId = $flight->fl_id ?? null;
        $this->defaultPrices = $defaultPrices;

        if ($systemMarkUp) {
            $this->serviceFeeAmount = $systemMarkUp;
        }

        if ($flight) {
            if ($this->defaultPrices) {
                $this->prices = FlightQuotePaxPriceHelper::getVoluntaryQuotePaxPriceFormCollection($flight, $systemMarkUp);
                $this->oldPrices = serialize(ArrayHelper::toArray($this->prices));
            }

            $this->serviceFee = ProductTypePaymentMethodQuery::getDefaultPercentFeeByProductType($flight->flProduct->pr_type_id) ?? (FlightQuote::SERVICE_FEE * 100);
            $this->currencyRate = ProductQuoteHelper::getClientCurrencyRate($flight->flProduct);
            $this->currencyCode = ProductQuoteHelper::getClientCurrencyCode($flight->flProduct);
        }
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['gds', 'pcc', 'tripType', 'cabin', 'validatingCarrier', 'fareType', 'reservationDump'], 'string'],
            [['gds', 'validatingCarrier', 'cabin', 'tripType', 'fareType', 'reservationDump', 'quoteCreator', 'keyTripList', 'pcc', 'expirationDate'], 'required'],
            ['quoteCreator', 'integer'],

            [['reservationDump'], 'string'],
            ['recordLocator', 'string', 'max' => 8],
            ['pcc', 'string', 'max' => 10],
            [['quoteCreator'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['quoteCreator' => 'id']],
            ['gds', 'in',  'range' => array_keys(ParsingDump::QUOTE_GDS_TYPE_MAP)],
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

            [['serviceFee'], 'safe'],

            [['prices'], IsArrayValidator::class, 'skipOnError' => true, 'skipOnEmpty' => true],
            [['prices'], 'priceProcessing'],

            [['oldPrices', 'customerPackage', 'serviceFeeCurrency'], 'safe'],

            [['serviceFeeAmount'], 'filter', 'filter' => static function ($value) {
                return empty($value) ? null : VoluntaryExchangeBOService::prepareFloat($value);
            }],
            ['expirationDate', NormalizeDateValidator::class],
            ['expirationDate', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function priceProcessing(string $attribute): void
    {
        if (!$this->defaultPrices && !empty($this->prices)) {
            foreach ($this->prices as $key => $price) {
                $form = new VoluntaryQuotePaxPriceForm($price['paxCode'], (int) $price['paxCodeId'], (int) $price['cnt']);
                $form->setFormName('');
                if (!$form->load($price)) {
                    $this->addError($attribute, 'PaxPriceForm[' . $price['paxCode'] . '] not loaded');
                } elseif (!$form->validate()) {
                    $this->addError($attribute, 'PaxPriceForm[' . $price['paxCode'] . '] ' . ErrorsToStringHelper::extractFromModel($form, ' '));
                } else {
                    $this->flightQuotePaxPriceForms[] = $form;
                }
            }
        }
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
        if (!$reservationService->parseStatus || empty($this->itinerary)) {
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

    public function setItinerary($itinerary): array
    {
        return $this->itinerary = $itinerary;
    }

    public function getSegmentTripFormsData(): array
    {
        return $this->segmentTripFormsData;
    }

    public function setSegmentTripFormsData($segmentTripFormsData): array
    {
        return $this->segmentTripFormsData = $segmentTripFormsData;
    }

    public function getFlightQuotePaxPriceForms(): array
    {
        return $this->flightQuotePaxPriceForms;
    }

    public function setCustomerPackage(?string $customerPackage): void
    {
        $this->customerPackage = $customerPackage;
    }

    public function setServiceFeeCurrency(?string $serviceFeeCurrency): void
    {
        $this->serviceFeeCurrency = $serviceFeeCurrency;
    }

    public function setServiceFeeAmount(?float $serviceFeeAmount): void
    {
        $this->serviceFeeAmount = $serviceFeeAmount;
    }
}
