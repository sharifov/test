<?php

namespace modules\flight\src\useCases\flightQuote\createManually;

use modules\flight\models\FlightPax;
use src\traits\FormNameModelTrait;
use yii\base\Model;

class FlightQuotePaxPriceForm extends Model
{
    use FormNameModelTrait;

    private const MAX_DECIMAL_VAL = 99999999.99;
    private const MIN_DECIMAL_VAL = 0;

    /**
     * @var float
     */
    public $selling;

    /**
     * @var float
     */
    public $net;

    /**
     * @var float
     */
    public $fare;

    /**
     * @var float
     */
    public $taxes;

    /**
     * @var float
     */
    public $markup;

    /**
     * @var string
     */
    public $paxCode;

    /**
     * @var integer
     */
    public $cnt;

    /**
     * @var float
     */
    public $clientSelling;

    /**
     * @var int
     */
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
        $this->selling = 0.00;
        $this->net = 0.00;
        $this->fare = 0.00;
        $this->taxes = 0.00;
        $this->markup = 0.00;
        $this->clientSelling = 0.00;
        $this->systemMarkUp = $systemMarkUp ?? 0.00;
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
            [['selling', 'net', 'fare', 'markup', 'clientSelling'], 'number', 'max' => self::MAX_DECIMAL_VAL, 'min' => self::MIN_DECIMAL_VAL],
            [['taxes'], 'number', 'max' => self::MAX_DECIMAL_VAL, 'min' => 0.01],
            [['paxCodeId'], 'integer'],
            [['paxCodeId'], 'in', 'range' => FlightPax::getPaxListId()],
            [['paxCode'], 'in', 'range' => FlightPax::getPaxList()],
        ];
    }

    public function attributeLabels()
    {
        return [
            'markup' => 'Extra Markup',
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
