<?php

namespace src\services\quote\addQuote\price;

use common\models\Currency;
use common\models\Quote;
use common\models\QuotePrice;
use yii\base\Model;

/**
 * Class QuotePriceSearchForm
 */
class QuotePriceSearchForm extends Model
{
    public $cnt;
    public $baseFare;
    public $baseTax;
    public $markup;
    public $clientExtraMarkUp;

    public string $paxCode;
    public bool $checkPayment;
    public ?string $currency;

    private Quote $quote;

    public function __construct(
        string $paxCode,
        bool $checkPayment,
        ?string $currency,
        Quote $quote,
        array $config = []
    ) {
        $this->paxCode = $paxCode;
        $this->checkPayment = $checkPayment;
        $this->currency = $currency;
        $this->quote = $quote;

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['baseFare', 'baseTax', 'markup'], 'required'],

            [['cnt'], 'integer'],

            [['baseFare', 'baseTax', 'markup', 'clientExtraMarkUp'], 'number'],

            [['paxCode'], 'string', 'max' => 3],
            [['paxCode'], 'in', 'range' => array_keys(QuotePrice::PASSENGER_TYPE_LIST)],

            [['baseTax'], 'number', 'min' => 0, 'when' => function () {
                return $this->paxCode !== QuotePrice::PASSENGER_INFANT;
            }],

            [['checkPayment'], 'boolean'],
            [['checkPayment'], 'default', 'value' => true],

            [['currency'], 'required'],
            [['currency'], 'string', 'max' => 3],
            [['currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['currency' => 'cur_code']],
        ];
    }

    public function formName(): string
    {
        return '';
    }

    public function getQuote(): Quote
    {
        return $this->quote;
    }
}
