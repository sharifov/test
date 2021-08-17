<?php

namespace modules\flight\src\useCases\reProtectionQuoteManualCreate\form;

use common\models\Airline;
use common\models\Employee;
use frontend\helpers\JsonHelper;
use modules\flight\models\Flight;
use modules\flight\models\FlightQuote;
use modules\flight\src\helpers\FlightQuoteHelper;
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
 *
 * @property $itinerary
 * @property $baggageFormsData
 */
class ReProtectionQuoteCreateForm extends Model
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

    private $itinerary = [];
    private array $baggageFormsData = [];

    public function __construct(?int $creatorId = null, $config = [])
    {
        $this->quoteCreator = $creatorId;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['gds', 'pcc', 'tripType', 'cabin', 'validatingCarrier', 'fareType', 'reservationDump'], 'string'],
            [['gds', 'validatingCarrier', 'cabin', 'tripType', 'fareType', 'reservationDump', 'quoteCreator'], 'required'],
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
        ];
    }

    public function baggageDataHandle(): void
    {
        if (!empty($this->baggage_data)) {
            parse_str($this->baggage_data, $this->baggageFormsData);
        }
    }

    public function checkReservationDump(): void
    {
        $dumpParser = FlightQuoteHelper::parseDump($this->reservationDump, true, $this->itinerary);
        if (empty($dumpParser)) {
            $this->addError('reservationDump', 'Incorrect reservation dump!');
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
}