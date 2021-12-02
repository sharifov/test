<?php

namespace modules\flight\src\useCases\flightQuote\createManually;

use modules\flight\models\FlightPax;
use sales\traits\FormNameModelTrait;
use yii\base\Model;

/**
 * Class VoluntaryQuotePaxPriceForm
 */
class VoluntaryQuotePaxPriceForm extends Model
{
    use FormNameModelTrait;

    public const MAX_DECIMAL_VAL = 99999999.99;
    public const MIN_DECIMAL_VAL = 0;

    public $selling;
    public $net;
    public $fare;
    public $taxes;
    public $markup;
    public $paxCode;
    public $cnt;
    public $clientSelling;
    public $paxCodeId;
    public $systemMarkUp;

    /**
     * @param string|null $paxCode
     * @param int|null $paxCodeId
     * @param int $cnt
     * @param float|null $systemMarkUp
     * @param array $config
     */
    public function __construct(
        ?string $paxCode = null,
        ?int $paxCodeId = null,
        int $cnt = 0,
        ?float $systemMarkUp = null,
        $config = []
    ) {
        $this->selling = $systemMarkUp ?: 0.00;
        $this->net = 0.00;
        $this->fare = 0.00;
        $this->taxes = 0.00;
        $this->markup = 0.00;
        $this->clientSelling = 0.00;
        $this->systemMarkUp = $systemMarkUp ?: 0.00;
        $this->paxCode = $paxCode;
        $this->paxCodeId = $paxCodeId;
        $this->cnt = $cnt;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['paxCode'], 'string'],
            [['selling', 'net', 'fare', 'taxes', 'markup', 'clientSelling', 'systemMarkUp'], 'filter', 'filter' => 'floatval'],
            [['selling', 'fare', 'taxes'], 'number', 'max' => self::MAX_DECIMAL_VAL, 'min' => self::MIN_DECIMAL_VAL],

            [['paxCodeId'], 'integer'],
            [['paxCodeId'], 'in', 'range' => FlightPax::getPaxListId()],
            [['paxCode'], 'in', 'range' => FlightPax::getPaxList()],
        ];
    }

    public function attributeLabels()
    {
        return [
            'markup' => 'Agent Markup',
            'selling' => 'Price',
            'fare' => 'Price Difference',
            'tax' => 'Airline Penalty',
            'systemMarkUp' => 'Processing Fee',
        ];
    }

    public static function getMaxDecimalVal(): float
    {
        return self::MAX_DECIMAL_VAL;
    }

    public static function getMinDecimalVal(): float
    {
        return self::MIN_DECIMAL_VAL;
    }
}
