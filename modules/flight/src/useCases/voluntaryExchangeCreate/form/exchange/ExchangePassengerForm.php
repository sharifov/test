<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\form\exchange;

use modules\flight\models\FlightPax;
use sales\traits\FormNameModelTrait;
use yii\base\Model;

/**
 * Class ExchangePassengerForm
 *
 * @property $paxCode
 * @property $paxCodeId
 * @property $cnt
 * @property $baseFare
 * @property $baseTax
 * @property $markup
 * @property $price
 */
class ExchangePassengerForm extends Model
{
    use FormNameModelTrait;

    public $paxCode;
    public $paxCodeId;

    public $cnt;
    public $baseFare;
    public $baseTax;
    public $markup;
    public $price;

    /**
     * @param string $paxCode
     * @param array $config
     */
    public function __construct(string $paxCode, array $config = [])
    {
        $this->paxCode = $paxCode;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['cnt'], 'required'],
            [['cnt'], 'integer'],


            [['paxCode'], 'string'],
            [['paxCode'], 'in', 'range' => FlightPax::getPaxList()],
            [['paxCode'], 'detectCodeId'],

            [['paxCodeId'], 'integer'],

            [['baseFare', 'baseTax', 'markup', 'price'], 'filter', 'filter' => 'floatval', 'skipOnEmpty' => true, 'skipOnError' => true],
            [['baseFare', 'baseTax', 'markup', 'price'], 'default', 'value' => 0.00],
            [['baseFare', 'baseTax', 'markup', 'price'], 'number'],
        ];
    }

    public function detectCodeId(string $attribute): void
    {
        if (!$paxCodeId = FlightPax::getPaxId($this->paxCode)) {
            $this->addError($attribute, 'PaxCodeId not found by (' . $this->paxCode . ')');
        } else {
            $this->paxCodeId = $paxCodeId;
        }
    }
}
