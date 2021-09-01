<?php

namespace sales\model\clientChatDataRequest\form;

use common\components\SearchService;
use yii\validators\DateValidator;

/**
 * @property-read string|mixed $cabinCode
 *
 * @property string $originIata
 * @property string $departureDate
 * @property string $cabin
 * @property string $returnDate
 * @property string $destinationIata
 * @property int $adults
 * @property int $children
 * @property int $infants
 * @property string $tripType
 */
class FlightSearchDataRequestForm extends \yii\base\Model
{
    private const ROUND_TRIP = 'rt';
    private const ONE_WAY = 'ow';
    private const MULTI_CITY = 'mc';

    private const CABIN_CODE_LIST = [
        'economy' => SearchService::CABIN_ECONOMY,
        'business' => SearchService::CABIN_BUSINESS,
        'first' => SearchService::CABIN_FIRST,
        'premium-first' => SearchService::CABIN_PREMIUM_FIRST,
        'premium-economy' => SearchService::CABIN_PREMIUM_ECONOMY,
        'premium-business' => SearchService::CABIN_PREMIUM_BUSINESS
    ];

    public string $originIata = '';
    public string $departureDate = '';
    public string $cabin = '';
    public string $returnDate = '';
    public string $destinationIata = '';
    public int $adults = 0;
    public int $children = 0;
    public int $infants = 0;
    public string $tripType = '';

    public function __construct(array $parameters, $config = [])
    {
        $this->originIata = $parameters['originIataParam'] ?? '';
        $this->departureDate = $parameters['departureDate'] ?? '';
        $this->cabin = $parameters['cabin'] ?? '';
        $this->returnDate = $parameters['returnDate'] ?? '';
        $this->destinationIata = $parameters['destinationIataParam'] ?? '';
        $this->adults = $parameters['paxes']['adults'] ?? 0;
        $this->children = $parameters['paxes']['children'] ?? 0;
        $this->infants = $parameters['paxes']['infants'] ?? 0;
        $this->tripType = $parameters['tripType'] ?? '';
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['originIata', 'departureDate', 'cabin', 'returnDate', 'destinationIata', 'tripType'], 'string'],
            [['originIata', 'departureDate', 'cabin', 'returnDate', 'destinationIata', 'tripType'], 'trim'],
            [['originIata', 'departureDate', 'cabin', 'returnDate', 'destinationIata', 'tripType'], 'default', 'value' => ''],
            [['departureDate', 'returnDate'], DateValidator::class, 'format' => 'Y-m-d'],
            [['adults', 'children', 'infants'], 'integer'],
            [['adults', 'children', 'infants'], 'default', 'value' => 0],

        ];
    }

    public function formName(): string
    {
        return '';
    }

    public function isRoundTrip(): bool
    {
        return $this->tripType === self::ROUND_TRIP;
    }

    public function getCabinCode()
    {
        return self::CABIN_CODE_LIST[$this->cabin] ?? $this->cabin;
    }
}
